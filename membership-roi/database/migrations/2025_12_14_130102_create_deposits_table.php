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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('deposit_address_id')->constrained('deposit_addresses')->restrictOnDelete();
            $table->foreignId('deposit_session_id')->nullable()->constrained('deposit_sessions')->nullOnDelete();

            $table->string('tx_hash', 80)->unique();
            $table->unsignedBigInteger('block_number')->nullable();
            $table->string('from_address', 64)->nullable();
            $table->string('to_address', 64);

            $table->string('token_symbol', 16)->default('USDT');
            $table->string('token_contract', 64)->nullable();
            $table->unsignedInteger('token_decimals')->default(18);
            $table->decimal('amount', 14, 2);

            $table->string('status')->default('detected'); // detected|credited|ignored
            $table->timestamp('detected_at')->nullable();
            $table->timestamp('credited_at')->nullable();
            $table->unsignedBigInteger('wallet_transaction_id')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['deposit_address_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
