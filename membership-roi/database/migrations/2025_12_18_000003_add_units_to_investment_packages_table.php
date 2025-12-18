<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investment_packages', function (Blueprint $table) {
            $table->unsignedInteger('total_units')->default(100)->after('is_active');
            $table->unsignedInteger('sold_units')->default(0)->after('total_units');

            $table->index(['is_active', 'amount']);
        });
    }

    public function down(): void
    {
        Schema::table('investment_packages', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'amount']);
            $table->dropColumn(['total_units', 'sold_units']);
        });
    }
};

