<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('password'); // customer|admin
            $table->string('phone', 20)->nullable()->after('role');
            $table->string('avatar_path')->nullable()->after('phone');
            $table->string('meal_category')->nullable()->after('avatar_path'); // veg|non_veg|vegan
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'avatar_path', 'meal_category']);
        });
    }
};
