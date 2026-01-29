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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->date('date_of_birth');
            $table->date('hire_date');
            $table->date('resign_date')->nullable();
            $table->foreignId('department_id')->constrained()->onDelete('restrict');
            $table->foreignId('position_id')->constrained()->onDelete('restrict');
            $table->decimal('salary', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'resigned'])->default('active');
            $table->text('address')->nullable();
            $table->timestamps();

            $table->index(['department_id', 'position_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('departments');
    }
};
