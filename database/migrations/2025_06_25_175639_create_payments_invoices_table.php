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
         |  PAYMENTS & INVOICES                                              |
         ------------------------------------------------------------------*/

        Schema::getConnection()->statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('order_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['carte', 'paypal', 'virement', 'especes'])->default('carte');
            $table->enum('status', ['en_attente', 'reussi', 'echoue', 'rembourse'])->default('en_attente');
            $table->string('transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index('transaction_id');
            $table->index(['status', 'paid_at']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('order_id')->unique();
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index('invoice_number');
            $table->index('issued_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
