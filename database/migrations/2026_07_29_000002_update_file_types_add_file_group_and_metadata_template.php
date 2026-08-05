<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('file_types', function (Blueprint $table) {
            $table->foreignId('file_group_id')->nullable()->after('id')->constrained('file_groups')->cascadeOnDelete();
            $table->json('metadata_template')->nullable()->after('name');
        });

        Schema::table('file_types', function (Blueprint $table) {
            $table->dropColumn('year');
        });
    }

    public function down(): void
    {
        Schema::table('file_types', function (Blueprint $table) {
            $table->string('year', 50)->nullable();
        });

        Schema::table('file_types', function (Blueprint $table) {
            $table->dropForeign(['file_group_id']);
            $table->dropColumn(['file_group_id', 'metadata_template']);
        });
    }
};
