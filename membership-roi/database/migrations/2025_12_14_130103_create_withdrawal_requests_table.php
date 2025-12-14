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
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('currency', 10)->default('USDT');
            $table->string('network')->default('BEP20');
            $table->string('to_address', 64);

            $table->decimal('amount', 14, 2);
            $table->string('fee_type'); // qos_15, qbit_10
            $table->decimal('fee_amount', 14, 2);
            $table->decimal('net_amount', 14, 2);

            $table->string('status')->default('pending'); // pending|approved|rejected|paid
            $table->unsignedBigInteger('wallet_transaction_id')->nullable();
            $table->foreignId('processed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->string('tx_hash', 80)->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index(['status', 'requested_at']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
