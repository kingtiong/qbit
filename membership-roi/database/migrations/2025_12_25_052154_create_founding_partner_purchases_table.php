<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('founding_partner_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('package'); // pro | pro_max
            $table->decimal('amount', 14, 2);
            $table->unsignedBigInteger('wallet_transaction_id')->nullable();
            $table->timestamp('purchased_at');
            $table->timestamps();

            // Each member can choose either 1 founding partner option.
            $table->unique('user_id');
            $table->index(['package', 'purchased_at']);
        });

        // Ensure the gate counter exists (used for safe locking during purchases).
        $now = now();
        DB::table('settings')->updateOrInsert(
            ['key' => 'qbp_founding_partner_sold'],
            ['value' => '0', 'created_at' => $now, 'updated_at' => $now],
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('founding_partner_purchases');
        // Do not delete settings key on rollback (best-effort).
    }
};

