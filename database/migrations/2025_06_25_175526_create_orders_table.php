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
         |  ORDERS & ORDER ITEMS                                             |
         ------------------------------------------------------------------*/
        Schema::getConnection()->statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');

        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->string('order_number')->unique(); // ✅ Numéro de commande unique
            $table->uuid('user_id');
            $table->enum('status', ['en_attente', 'confirme', 'en_production', 'pret', 'expedie', 'livre', 'annule'])->default('en_attente');
            $table->decimal('total', 10, 2);
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_country')->nullable();
            $table->string('shipping_zip', 20)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'status']);
            $table->index('created_at');
            $table->index('order_number');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('order_id');
            $table->uuid('product_id');
            $table->integer('quantity');
            $table->string('size')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict'); // Éviter suppression produit avec commandes
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
    }
};
