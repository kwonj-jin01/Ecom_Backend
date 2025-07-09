<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));

            // Clé étrangère vers la commande
            $table->uuid('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            // Adresse de livraison spécifique
            $table->text('adresse_livraison')->nullable();
            $table->string('transporteur')->nullable();
            $table->enum('statut', ['en_preparation', 'en_transit', 'livre', 'retarde'])->default('en_preparation');
            $table->date('date_estimee')->nullable();
            $table->timestamps();

            // Index utiles
            $table->index('order_id');
            $table->index('statut');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
