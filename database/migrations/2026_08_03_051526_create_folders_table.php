<?php

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

        Schema::create('folders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_id')->nullable()->constrained('folders', 'id')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constraied('users', 'id')->nullOnDelete();
            $table->string('name', 100)->nullable(false);
            $table->string('path', 500)->unique()->nullable(false);
            $table->text('description')->nullable(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->unique(['parent_id', 'name'], 'unique_folder_name_per_parent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
