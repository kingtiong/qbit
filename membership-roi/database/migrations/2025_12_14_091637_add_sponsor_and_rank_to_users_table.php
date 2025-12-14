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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('sponsor_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->string('rank')->default('B')->after('is_admin'); // B|A|S|SS|SSS

            $table->index(['sponsor_id']);
            $table->index(['rank']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['sponsor_id']);
            $table->dropIndex(['rank']);
            $table->dropConstrainedForeignId('sponsor_id');
            $table->dropColumn(['rank']);
        });
    }
};
