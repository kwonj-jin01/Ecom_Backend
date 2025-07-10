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
        Schema::getConnection()->statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');

        Schema::create('cart_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->string('numero_panier')->unique(); // ✅ Numéro unique pour le panier
            $table->uuid('user_id');
            $table->uuid('product_id');
            $table->integer('quantity')->default(1);
            $table->string('size')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Index composé pour éviter les doublons dans le panier
            $table->unique(['user_id', 'product_id', 'size']);
            $table->index('user_id');
            $table->index('numero_panier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
