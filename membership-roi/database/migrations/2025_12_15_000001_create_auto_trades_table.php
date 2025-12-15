<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('trade_date');
            $table->string('symbol', 24); // e.g. BTC, ETH
            $table->string('pair', 32)->default('USDT'); // displayed as SYMBOL/USDT
            $table->decimal('buy_price', 18, 8);
            $table->decimal('sell_price', 18, 8);
            $table->decimal('qty', 18, 8);
            $table->decimal('pnl', 18, 2);
            $table->decimal('pnl_pct', 8, 4); // percent (e.g. 0.75)
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'trade_date', 'symbol']);
            $table->index(['user_id', 'trade_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_trades');
    }
};

