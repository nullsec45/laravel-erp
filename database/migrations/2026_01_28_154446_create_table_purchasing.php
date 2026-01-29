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

        // Vendors
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->string('contact_person', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('website', 100)->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->string('payment_terms', 100)->nullable();
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index('code');
            $table->index('name');
            $table->index('is_active');
        });

        // Purchase Orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 50)->unique();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'partial', 'completed', 'cancelled'])->default('draft');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('outstanding_amount', 15, 2)->default(0);
            $table->string('payment_terms', 100)->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();

            $table->index('po_number');
            $table->index('order_date');
            $table->index('status');
        });

        // Purchase Order Items
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 2);
            $table->decimal('received_quantity', 12, 2)->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();

            $table->index(['purchase_order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('vendors');
    }
};
