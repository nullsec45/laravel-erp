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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->onDelete('cascade');

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('slug');
            $table->index('parent_id');
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('slug');
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Kilogram
            $table->string('short_name'); // e.g., Kg
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('units');
    }
};
