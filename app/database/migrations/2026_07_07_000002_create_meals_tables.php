<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category'); // veg|non_veg|vegan
            $table->string('meal_time'); // breakfast|lunch|dinner
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('calories');
            $table->decimal('protein_g', 6, 1)->default(0);
            $table->decimal('carbs_g', 6, 1)->default(0);
            $table->decimal('fat_g', 6, 1)->default(0);
            $table->decimal('fiber_g', 6, 1)->default(0);
            $table->string('image_path')->nullable();
            $table->string('badge')->nullable(); // e.g. High Protein, Low Carb
            $table->unsignedSmallInteger('prep_time_minutes')->default(10);
            $table->unsignedSmallInteger('cook_time_minutes')->default(15);
            $table->unsignedTinyInteger('spice_level')->default(1); // 1-3
            $table->json('allergens')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['category', 'meal_time', 'is_active']);
        });

        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('emoji', 16)->nullable(); // used in animated ingredient fly-ins
            $table->timestamps();
        });

        Schema::create('meal_ingredient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->string('quantity'); // e.g. "120 g", "1 tbsp"
            $table->unsignedInteger('sort_order')->default(0);
            $table->unique(['meal_id', 'ingredient_id']);
        });

        Schema::create('cooking_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('step_number');
            $table->string('title');
            $table->text('instruction');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->timestamps();

            $table->unique(['meal_id', 'step_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cooking_steps');
        Schema::dropIfExists('meal_ingredient');
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('meals');
    }
};
