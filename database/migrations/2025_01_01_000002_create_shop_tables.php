<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->string('icon')->default('percent');
            $table->string('type')->default('fixed'); // fixed | percent
            $table->decimal('value', 10, 2)->default(0);
            $table->decimal('min_order', 10, 2)->default(0);
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->date('expires_at')->nullable();
            $table->boolean('show_in_profile')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('delivery_slots', function (Blueprint $table) {
            $table->id();
            $table->string('day_label');
            $table->string('title');
            $table->string('time_range')->nullable();
            $table->string('badge')->nullable();
            $table->boolean('is_custom')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('icon')->default('home');
            $table->string('line1');
            $table->string('line2')->nullable();
            $table->string('recipient')->nullable();
            $table->string('phone')->nullable();
            $table->string('note')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address');
            $table->string('city')->nullable();
            $table->string('instructions')->nullable();
            $table->string('slot_label')->nullable();
            $table->date('custom_date')->nullable();
            $table->text('gift_note')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->string('status')->default('confirmed');
            $table->string('payment_method')->default('whatsapp');
            $table->string('payment_status')->default('pending');
            $table->string('payment_note')->nullable();
            $table->string('target_arrival')->nullable();
            $table->string('status_note')->nullable();
            $table->text('tracking_url')->nullable();
            $table->string('label')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->string('review')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('detail')->nullable();
            $table->text('image')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('qty');
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['order_items', 'orders', 'addresses', 'delivery_slots', 'coupons'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
