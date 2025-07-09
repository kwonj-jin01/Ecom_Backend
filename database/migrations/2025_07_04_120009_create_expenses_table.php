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

        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid(column: 'id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->string('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
