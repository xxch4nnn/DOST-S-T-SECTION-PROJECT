<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('file_id')->constrained('files', 'id')->cascadeOnDelete();
            $table->string('file_name', 200)->unique()->nullable(false);
            $table->string('file_path', 500)->unique()->nullable(false);
            $table->integer('file_size_kb')->nullable(false);
            $table->integer('version_number')->nullable(false);
            $table->foreignId('replaced_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamp('replaced_at')->nullable(false)->useCurrentOnUpdate();
            $table->unique(['file_id', 'version_number'], 'version_number_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};
