<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrative_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_type', 100);
            $table->string('series_number', 100)->nullable();
            $table->string('title', 255);
            $table->string('recipient', 150)->nullable();
            $table->integer('year')->nullable();
            $table->string('quarter', 10)->nullable();
            $table->boolean('for_disposal')->default(false);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrative_records');
    }
};
