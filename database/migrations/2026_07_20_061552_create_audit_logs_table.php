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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable(false)->constrained('users')->restrictOnDelete(); // Who did it
            $table->string('action')->nullable(false); // e.g., 'created', 'updated', 'deleted'
            
            // Polymorphic columns: what table and what row was affected
            $table->string('loggable_type')->nullable(false); // e.g., 'App\Models\Scholar'
            $table->unsignedBigInteger('loggable_id')->nullable(false); // e.g., Scholar ID #45
            
            // The JSON snapshot of what exactly changed
            $table->json('before_payload')->nullable();
            $table->json('after_payload')->nullable();
            
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
