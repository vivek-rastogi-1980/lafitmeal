<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            $table->string('meal_category'); // veg|non_veg|vegan
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('duration_days'); // minimum 15
            $table->string('status')->default('pending_payment'); // pending_payment|active|paused|completed|cancelled
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('wallet_applied', 10, 2)->default(0);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->date('paused_from')->nullable();
            $table->date('paused_until')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // Weekly template: which meal is planned for each weekday/meal-time slot.
        Schema::create('subscription_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0=Sun .. 6=Sat
            $table->string('meal_time'); // breakfast|lunch|dinner
            $table->foreignId('meal_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->unique(['subscription_id', 'day_of_week', 'meal_time']);
        });

        // Concrete per-date schedule generated from the template.
        Schema::create('meal_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meal_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->string('meal_time'); // breakfast|lunch|dinner
            $table->decimal('unit_price', 10, 2);
            $table->string('status')->default('scheduled'); // scheduled|skipped|locked|preparing|out_for_delivery|delivered|missed
            $table->timestamp('skipped_at')->nullable();
            $table->timestamps();

            $table->unique(['subscription_id', 'date', 'meal_time']);
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_schedules');
        Schema::dropIfExists('subscription_slots');
        Schema::dropIfExists('subscriptions');
    }
};
