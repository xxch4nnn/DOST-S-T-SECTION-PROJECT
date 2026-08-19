<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RFC docs/db/DOCUMENTS_UUID_RFC.md — Migration 2.
 * Drop file columns from documents (now on document_versions).
 * Keep bigint id PK + uuid dual-key for V1 rollback safety.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('file_type_id');
            $table->dropConstrainedForeignId('uploaded_by');
            $table->dropColumn([
                'original_filename',
                'stored_filename',
                'mime_type',
                'file_size_kb',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('file_type_id')->nullable()->after('documentable_id')
                ->constrained('file_types')->restrictOnDelete();
            $table->string('original_filename', 255)->nullable()->after('file_type_id');
            $table->string('stored_filename', 100)->nullable()->after('original_filename');
            $table->string('mime_type', 100)->nullable()->after('stored_filename');
            $table->integer('file_size_kb')->nullable()->after('mime_type');
            $table->foreignId('uploaded_by')->nullable()->after('metadata')
                ->constrained('users')->restrictOnDelete();
        });
    }
};
