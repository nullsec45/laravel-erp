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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('company_favicon')->nullable();
            $table->string('currency', 10)->default('IDR');
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('H:i');
            $table->string('language', 5)->default('en');
            $table->string('fiscal_year_start')->default('January');
            $table->string('tax_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('theme')->default('light');

            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('action');
            $table->text('description')->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
            $table->index('user_id');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
        Schema::dropIfExists('activity_logs');
    }
};