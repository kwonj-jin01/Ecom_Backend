<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /* ------------------------------------------------------------------
         |  CATALOG: CATEGORIES & PRODUCTS                                  |
         ------------------------------------------------------------------*/

        Schema::getConnection()->statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');

        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->string('name');
            $table->text('image');
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->string('title');
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('brand');
            $table->string('gender');
            $table->text('thumbnail');
            $table->text('image');
            $table->text('hover_image')->nullable();
            $table->boolean('is_new')->default(false);
            $table->boolean('is_best_seller')->default(false);
            $table->boolean('in_stock')->default(true);
            $table->boolean('is_on_sale')->default(false);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->decimal('discount', 5, 2)->nullable();
            $table->string('promotion')->nullable();
            $table->uuid('category_id');
            $table->timestamps();

            // Contraintes et index
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->index(['category_id', 'is_new', 'is_best_seller']);
            $table->index('brand');
            $table->index('gender');
            $table->index(['price', 'is_on_sale']);
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('product_id');
            $table->text('url');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index('product_id');
        });

        Schema::create('product_sizes', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('product_id');
            $table->string('size');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index('product_id');
            $table->unique(['product_id', 'size']); // Éviter les doublons
        });

        Schema::create('product_colors', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('product_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index('product_id');
            $table->unique(['product_id', 'name']); // Éviter les doublons
        });

        Schema::create('product_details', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('product_id');
            $table->string('label');
            $table->text('value');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index('product_id');
            $table->index('label');
        });

        Schema::create('abouts', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('product_id');
            $table->text('content');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique('product_id'); // Un seul about par produit
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_sizes');
        Schema::dropIfExists('product_colors');
        Schema::dropIfExists('product_details');
        Schema::dropIfExists('abouts');
    }
};
