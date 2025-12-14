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
        Schema::create('partnership_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('partnership_package_id')->constrained('partnership_packages')->restrictOnDelete();
            $table->string('status')->default('active'); // active|closed
            $table->timestamp('purchased_at');
            $table->timestamps();

            $table->unique(['user_id', 'partnership_package_id']);
            $table->index(['partnership_package_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partnership_positions');
    }
};
