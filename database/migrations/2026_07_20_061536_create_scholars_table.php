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
        Schema::create('scholars', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100)->nullable(false);
            $table->string('middle_name', 100)->nullable(true)->default(null);
            $table->string('last_name', 100)->nullable(false);
            $table->string('generational_suffix', 5)->nullable(true)->default(null);

            $table->integer('year_of_award')->nullable(false);
            $table->foreignId('scholarship_id')->nullable(false)->constrained('scholarships')->restrictOnDelete();
            $table->foreignId('scholarship_type_id')->nullable(false)->constrained('scholarship_types')->restrictOnDelete();
            $table->string('spas_no', 100)->unique()->nullable();

            $table->enum('sex', ['Male', 'Female'])->nullable();
            $table->date('birthdate')->nullable();
            $table->string('contact_number', 30)->unique()->nullable(false);
            $table->string('email_address')->nullable();
            $table->foreignId('school_id')->nullable(false)->constrained('schools')->restrictOnDelete();
            $table->foreignId('course_id')->nullable(false)->constrained('courses')->restrictOnDelete();

           
            $table->foreignId('region_id')->nullable(false)->constrained('regions')->restrictOnDelete();
            $table->foreignId('province_id')->nullable()->constrained('provinces')->restrictOnDelete();
            $table->foreignId('municipality_id')->nullable()->constrained('municipalities')->restrictOnDelete();
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->restrictOnDelete();
            $table->string('home_address', 200)->nullable(true)->default(null);

            $table->foreignId('clearance_status_id')->nullable(false)->constrained('clearance_statuses')->restrictOnDelete()->default(0);
            $table->date('clearance_date')->nullable();
            $table->boolean('for_disposal')->nullable(false)->default(false);

            // Flattened Column for Full-Text Search
            $table->text('fts_search_data')->nullable();

            // Unique composite key for the scholar
            $table->unique(['first_name', 'middle_name', 'last_name', 'generational_suffix'], 'unique_scholar_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholars');
    }
};
