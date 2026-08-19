<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('document_uuid')->constrained('documents', 'uuid')->cascadeOnDelete();
            $table->foreignId('file_type_id')->nullable()->constrained('file_types')->nullOnDelete();
            $table->string('original_filename', 255)->nullable();
            $table->string('stored_filename', 255)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->integer('version_number')->default(1);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['document_uuid', 'version_number'], 'version_number_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};

