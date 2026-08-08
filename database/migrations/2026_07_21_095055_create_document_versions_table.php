<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id('id')->primary();
            $table->foreignUuid('document_id')->constrained('documents', 'id')->cascadeOnDelete();
            $table->integer('version_number')->nullable(false);
            $table->string('file_name', 200)->nullable(false);
            $table->string('file_path', 500)->unique()->nullable(false);
            $table->integer('file_size')->nullable(false);
            $table->foreignId('file_type_id')->nullable(false)->constrained('file_types', 'id')->restrictOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('uploaded_at')->default(now());
            $table->unique(['document_id', 'version_number'], 'version_number_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};
