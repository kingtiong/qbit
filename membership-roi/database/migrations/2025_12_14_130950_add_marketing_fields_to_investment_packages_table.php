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
            $table->text('summary')->nullable()->after('label');
            $table->json('benefits')->nullable()->after('summary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_packages', function (Blueprint $table) {
            $table->dropColumn(['summary', 'benefits']);
        });
    }
};
