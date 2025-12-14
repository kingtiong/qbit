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
        Schema::create('roi_rates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            // Stored as fractional rate: 0.00500 => 0.5%, 0.00800 => 0.8%
            $table->decimal('rate', 7, 5);
            $table->foreignId('set_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique(['date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roi_rates');
    }
};
