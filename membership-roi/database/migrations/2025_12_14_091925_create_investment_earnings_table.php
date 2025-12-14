<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('investment_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->constrained('investments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('source'); // qos_daily, direct_sponsor, rank_bonus, partnership_group, partnership_global, etc.
            $table->decimal('amount', 14, 2);
            $table->unsignedBigInteger('wallet_transaction_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['investment_id', 'date', 'source']);
            $table->index(['user_id', 'date', 'source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_earnings');
    }
};
