<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /* ------------------------------------------------------------------
         |  PRODUCTION TRACKING                                             |
         ------------------------------------------------------------------*/

        Schema::create('productions', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->string('numero_production')->unique(); // ✅ Numéro de production unique
            $table->uuid('order_id');
            $table->enum('statut', ['en_attente', 'en_cours', 'termine', 'controle_qualite', 'pret_expedition'])->default('en_attente');
            $table->date('date_debut')->nullable();
            $table->date('date_fin_prevue')->nullable();
            $table->date('date_fin_reelle')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index('order_id');
            $table->index('statut');
            $table->index('numero_production');
        });

        /* ------------------------------------------------------------------
         |  DELIVERIES                                                      |
         ------------------------------------------------------------------*/

        Schema::create('deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->string('numero_livraison')->unique(); // ✅ Numéro de livraison unique
            $table->uuid('order_id');
            $table->text('adresse_livraison')->nullable();
            $table->string('transporteur')->nullable();
            $table->string('numero_suivi')->nullable(); // Numéro de suivi du transporteur
            $table->enum('statut', ['en_preparation', 'en_transit', 'livre', 'retarde', 'echec_livraison'])->default('en_preparation');
            $table->date('date_estimee')->nullable();
            $table->date('date_livraison_reelle')->nullable();
            $table->text('commentaire_livraison')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index('order_id');
            $table->index('statut');
            $table->index('numero_livraison');
            $table->index('numero_suivi');
        });

        /* ------------------------------------------------------------------
         |  ORDER TRACKING (Historique des statuts)                        |
         ------------------------------------------------------------------*/

        Schema::create('order_tracking', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('order_id');
            $table->string('status');
            $table->text('description')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index('order_id');
            $table->index('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('order_tracking');
    }
};
