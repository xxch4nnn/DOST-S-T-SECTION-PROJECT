<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable'); // Creates documentable_id and documentable_type with index
            $table->foreignId('file_type_id')->constrained()->restrictOnDelete();
            $table->string('original_filename', 255);
            $table->string('stored_filename', 100);
            $table->string('mime_type', 100);
            $table->integer('file_size_kb');
            $table->enum('status', ['active', 'struck_off'])->default('active');
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
