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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->string('model');
            $table->integer('capacity_kg')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('license_number');
            $table->string('license_type');
            $table->enum('status', ['available', 'on_duty', 'off'])->default('available');
            $table->timestamps();
        });

        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('zone_code');
            $table->string('origin_city');
            $table->string('destination_city');
            $table->integer('distance_km')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('do_number')->unique(); // e.g., DO-2026-0001
            $table->foreignId('sales_order_id')->constrained('sales_orders');
            $table->string('customer_name');
            $table->text('shipping_address');
            $table->enum('status', ['draft', 'ready_to_ship', 'shipped', 'delivered', 'cancelled'])->default('draft');
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique();
            $table->foreignId('delivery_order_id')->constrained('delivery_orders')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('route_id')->nullable()->constrained('routes')->nullOnDelete();

            $table->dateTime('departure_time')->nullable();
            $table->dateTime('arrival_time_estimate')->nullable();
            $table->dateTime('arrival_time_actual')->nullable();
            $table->timestamps();
        });

        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('status');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('event_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('delivery_orders');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('vehicles');
    }
};
