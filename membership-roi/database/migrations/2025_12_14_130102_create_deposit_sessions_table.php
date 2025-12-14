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
        Schema::create('deposit_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('deposit_address_id')->constrained('deposit_addresses')->restrictOnDelete();
            $table->string('currency', 10)->default('USDT');
            $table->string('network')->default('BEP20');
            $table->string('status')->default('active'); // active|expired|completed
            $table->timestamp('reserved_until');
            $table->timestamp('completed_at')->nullable();
            $table->decimal('credited_amount', 14, 2)->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['deposit_address_id', 'status', 'reserved_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_sessions');
    }
};
