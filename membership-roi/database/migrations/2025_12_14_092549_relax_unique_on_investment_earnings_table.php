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
        Schema::table('investment_earnings', function (Blueprint $table) {
            $table->dropUnique(['investment_id', 'date', 'source']);
            $table->index(['investment_id', 'date', 'source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_earnings', function (Blueprint $table) {
            $table->dropIndex(['investment_id', 'date', 'source']);
            $table->unique(['investment_id', 'date', 'source']);
        });
    }
};
