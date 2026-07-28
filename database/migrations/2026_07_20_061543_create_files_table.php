<?php

use App\Models\FileType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_type_id')->nullable(false)->constrained('file_types')->restrictOnDelete();
            $table->string('file_name', 200)->unique()->nullable(false);
            $table->string('file_path', 500)->unique()->nullable(false);
            $table->integer('file_size')->nullable(false);
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('mime_type', 50)->nullable(false);
            $table->softDeletes('deleted_at')->nullable(true);
            $table->json('metadata')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
