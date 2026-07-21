<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholars', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('last_name', 50);
            $table->string('generational_suffix', 5)->nullable();
            $table->integer('year_of_award');
            $table->foreignId('scholarship_id')->constrained()->restrictOnDelete();
            $table->foreignId('scholarship_type_id')->constrained()->restrictOnDelete();
            $table->string('spas_no', 50)->nullable()->index();
            $table->string('sex', 6)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('contact_number', 11)->nullable();
            $table->string('email_address', 70)->nullable()->unique();
            $table->foreignId('school_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('program', 150)->nullable();
            $table->string('barangay', 150)->nullable();
            $table->string('municipality', 150)->nullable();
            $table->string('district', 150)->nullable();
            $table->string('province', 150)->nullable();
            $table->foreignId('region_id')->constrained()->restrictOnDelete();
            $table->foreignId('clearance_status_id')->default(1)->constrained()->restrictOnDelete();
            $table->date('clearance_date')->nullable();
            $table->boolean('for_disposal')->default(false);
            $table->timestamps();

            $table->index('last_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholars');
    }
};
