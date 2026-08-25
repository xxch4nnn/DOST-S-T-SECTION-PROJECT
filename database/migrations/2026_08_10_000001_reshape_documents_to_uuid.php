<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * RFC docs/db/DOCUMENTS_UUID_RFC.md — Migration 1.
 * Dual-key: keep documents.id (bigint PK); add unique uuid for app/version FK.
 * file_path: relative from storage root (e.g. documents/{stored_filename}).
 * file_size_bytes: bigint.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('documents', 'uuid')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            });
        }

        DB::table('documents')->orderBy('id')->each(function (object $doc): void {
            DB::table('documents')->where('id', $doc->id)->update([
                'uuid' => (string) Str::uuid(),
            ]);
        });

        if (! Schema::hasColumn('document_versions', 'document_uuid')) {
            Schema::table('document_versions', function (Blueprint $table) {
                $table->uuid('document_uuid')->nullable()->after('document_id');
                if (! Schema::hasColumn('document_versions', 'file_type_id')) {
                    $table->foreignId('file_type_id')->nullable()->after('document_uuid')
                        ->constrained('file_types')->nullOnDelete();
                }
                if (! Schema::hasColumn('document_versions', 'file_path')) {
                    $table->string('file_path', 500)->nullable()->after('original_filename');
                }
                if (! Schema::hasColumn('document_versions', 'mime_type')) {
                    $table->string('mime_type', 100)->nullable()->after('file_path');
                }
                if (! Schema::hasColumn('document_versions', 'file_size_bytes')) {
                    $table->unsignedBigInteger('file_size_bytes')->nullable()->after('mime_type');
                }
                if (! Schema::hasColumn('document_versions', 'uploaded_by')) {
                    $table->foreignId('uploaded_by')->nullable()->after('file_size_bytes')
                        ->constrained('users')->nullOnDelete();
                }
            });
        }

        // Ensure every document has at least one version row before column moves.
        DB::table('documents')->orderBy('id')->each(function (object $doc): void {
            $hasVersion = DB::table('document_versions')
                ->where('document_id', $doc->id)
                ->exists();

            if ($hasVersion) {
                return;
            }

            $stored = (string) $doc->stored_filename;
            DB::table('document_versions')->insert([
                'document_id' => $doc->id,
                'document_uuid' => $doc->uuid,
                'file_type_id' => $doc->file_type_id,
                'stored_filename' => $stored,
                'original_filename' => $doc->original_filename,
                'file_path' => 'documents/'.$stored,
                'mime_type' => $doc->mime_type,
                'file_size_kb' => $doc->file_size_kb,
                'file_size_bytes' => max(0, (int) $doc->file_size_kb) * 1024,
                'version_number' => 1,
                'replaced_by_user_id' => $doc->uploaded_by,
                'uploaded_by' => $doc->uploaded_by,
                'created_at' => $doc->created_at ?? now(),
                'updated_at' => $doc->updated_at ?? now(),
            ]);
        });

        $documents = DB::table('documents')->get()->keyBy('id');

        DB::table('document_versions')->orderBy('id')->each(function (object $version) use ($documents): void {
            $doc = $documents->get($version->document_id);
            if (! $doc) {
                return;
            }

            $stored = $version->stored_filename ?: $doc->stored_filename;
            $sizeKb = $version->file_size_kb ?? $doc->file_size_kb ?? 0;

            DB::table('document_versions')->where('id', $version->id)->update([
                'document_uuid' => $doc->uuid,
                'file_type_id' => $version->file_type_id ?? $doc->file_type_id,
                'file_path' => $version->file_path ?: ('documents/'.$stored),
                'mime_type' => $version->mime_type ?? $doc->mime_type,
                'file_size_bytes' => $version->file_size_bytes ?? (max(0, (int) $sizeKb) * 1024),
                'uploaded_by' => $version->uploaded_by ?? $version->replaced_by_user_id ?? $doc->uploaded_by,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('document_versions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('uploaded_by');
            $table->dropConstrainedForeignId('file_type_id');
            $table->dropColumn(['document_uuid', 'file_path', 'mime_type', 'file_size_bytes']);
        });

        Schema::table('documents', function (Blueprint $table) {
            // SQLite cannot drop a column while its unique index still exists.
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
