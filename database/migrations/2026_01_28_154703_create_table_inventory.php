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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false)->after('is_active');
            $table->timestamps();

            $table->index('code');
            $table->index('is_active');
        });

        Schema::create('stock_levels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');

            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('reserved_quantity', 15, 2)->default(0);

            $table->timestamps();

            $table->unique(['product_id', 'warehouse_id']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');

            $table->dateTime('date');


            $table->enum('type', ['in', 'out', 'transfer', 'adjustment']);

            $table->decimal('quantity', 15, 2);

            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'warehouse_id', 'date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_levels');
        Schema::dropIfExists('warehouses');
    }
};