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
        Schema::table('investments', function (Blueprint $table) {
            $table->decimal('max_return_amount', 14, 2)->nullable()->after('total_earned');
            $table->date('capped_on')->nullable()->after('last_accrued_on');
            $table->string('capped_reason')->nullable()->after('capped_on');

            $table->index(['capped_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropIndex(['capped_on']);
            $table->dropColumn(['max_return_amount', 'capped_on', 'capped_reason']);
        });
    }
};
