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
        Schema::table('investment_packages', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
            $table->decimal('daily_qos_amount', 14, 2)->nullable()->after('amount');
            $table->decimal('max_return_multiplier', 6, 2)->nullable()->after('daily_qos_amount');

            $table->index(['code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_packages', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->dropColumn(['code', 'daily_qos_amount', 'max_return_multiplier']);
        });
    }
};
