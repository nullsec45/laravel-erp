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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code', 20)->unique();
            $table->string('name', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();

            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->integer('payment_terms')->default(0)->comment('In days');

            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index('customer_code');
            $table->index('name');
            $table->index('is_active');
        });

        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // Pembuat penawaran
            $table->date('quotation_date');
            $table->date('valid_until');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', [
                'draft',
                'sent',
                'accepted',
                'rejected',
                'expired'
            ])->default('draft');
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->timestamps();
            $table->index('quotation_number');
            $table->index(['quotation_date', 'status']);
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quotation_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->timestamps();
        });

        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->foreignId('quotation_id')->nullable()->constrained()->onDelete('set null');
            $table->date('order_date');
            $table->date('delivery_date')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'shipped',
                'delivered',
                'cancelled'
            ])->default('pending');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_date', 'status']);
            $table->index('payment_status');
        });

        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_order_id')->nullable()->constrained()->onDelete('set null');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->string('payment_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('invoice_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->date('payment_date');
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'credit_card',
                'check'
            ])->default('cash');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['payment_date', 'payment_method']);
            $table->index('payment_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('customers');
    }
};
