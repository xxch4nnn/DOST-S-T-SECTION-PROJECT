<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * RFC docs/db/DOCUMENTS_UUID_RFC.md — Migration 3.
 * document_uuid becomes the canonical FK; drop legacy document_id + KB/replaced_by columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Safety: refuse null document_uuid before dropping document_id.
        $orphans = DB::table('document_versions')->whereNull('document_uuid')->count();
        if ($orphans > 0) {
            throw new RuntimeException("Cannot finalize document_versions: {$orphans} row(s) missing document_uuid.");
        }

        Schema::table('document_versions', function (Blueprint $table) {
            if (Schema::hasColumn('document_versions', 'document_id')) {
                $table->dropConstrainedForeignId('document_id');
            }
            if (Schema::hasColumn('document_versions', 'replaced_by_user_id')) {
                $table->dropConstrainedForeignId('replaced_by_user_id');
            }
            if (Schema::hasColumn('document_versions', 'file_size_kb')) {
                $table->dropColumn('file_size_kb');
            }
        });
    }

    public function down(): void
    {
        Schema::table('document_versions', function (Blueprint $table) {
            $table->dropForeign(['document_uuid']);
        });

        Schema::table('document_versions', function (Blueprint $table) {
            $table->unsignedBigInteger('document_id')->nullable()->after('id');
            $table->integer('file_size_kb')->nullable()->after('original_filename');
            $table->foreignId('replaced_by_user_id')->nullable()->after('version_number')
                ->constrained('users')->restrictOnDelete();
        });

        $docs = DB::table('documents')->pluck('id', 'uuid');
        DB::table('document_versions')->orderBy('id')->each(function (object $version) use ($docs): void {
            DB::table('document_versions')->where('id', $version->id)->update([
                'document_id' => $docs[$version->document_uuid] ?? null,
                'file_size_kb' => max(1, (int) ceil(($version->file_size_bytes ?? 0) / 1024)),
                'replaced_by_user_id' => $version->uploaded_by,
            ]);
        });

        Schema::table('document_versions', function (Blueprint $table) {
            $table->foreign('document_id')
                ->references('id')
                ->on('documents')
                ->cascadeOnDelete();
        });
    }
};
