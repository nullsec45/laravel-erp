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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->text('description')->nullable();

            // Relasi ke Master Data (Pastikan tabel ini sudah ada atau buat nullable)
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');

            $table->string('barcode')->nullable();
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);

            // Stock Management
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->default(0);
            $table->integer('reorder_level')->default(0);

            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();
            $table->string('type')->default('product'); // product, service, raw_material

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
