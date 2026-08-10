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
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            
            // Polymorphic parent link:
            // - Scholarly Files: documentable_type = 'App\Models\Scholar', documentable_id = '45'
            // - Drive Files:     documentable_type = 'App\Models\Folder',  documentable_id = 'uuid-string'
            $table->string('documentable_type', 150)->nullable(false);
            $table->string('documentable_id', 45)->nullable(false);

            $table->string('status', 50)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Compound index for high-performance polymorphic queries
            $table->index(['documentable_type', 'documentable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
