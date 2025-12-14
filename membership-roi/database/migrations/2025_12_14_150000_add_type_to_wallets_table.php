<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('wallets', 'type')) {
                $table->string('type')->default('registered')->after('user_id');
            }
        });

        // Backfill existing rows to registered.
        DB::table('wallets')
            ->whereNull('type')
            ->update(['type' => 'registered']);

        // Replace unique(user_id) with unique(user_id, type).
        //
        // NOTE: In MySQL, a foreign key column must be indexed. Our `wallets.user_id`
        // foreign key may be using the existing unique index (`wallets_user_id_unique`),
        // so we must drop/recreate the FK (or provide a replacement index) before dropping it.
        Schema::table('wallets', function (Blueprint $table) {
            try {
                $table->dropForeign(['user_id']);
            } catch (\Throwable $e) {
                // ignore (constraint name may differ or already dropped)
            }
        });

        Schema::table('wallets', function (Blueprint $table) {
            try {
                $table->dropUnique(['user_id']);
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                $table->unique(['user_id', 'type']);
            } catch (\Throwable $e) {
                // ignore
            }
        });

        Schema::table('wallets', function (Blueprint $table) {
            try {
                // Re-add foreign key constraint (do NOT re-add the column).
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            } catch (\Throwable $e) {
                // ignore (already exists)
            }
        });

        // Ensure every user has a commission wallet (0 balance).
        $now = now();
        $select = DB::table('users')
            ->select([
                'users.id as user_id',
                DB::raw("'commission' as type"),
                DB::raw("'0.00' as balance"),
                DB::raw("'".$now->toDateTimeString()."' as created_at"),
                DB::raw("'".$now->toDateTimeString()."' as updated_at"),
            ])
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('wallets')
                    ->whereColumn('wallets.user_id', 'users.id')
                    ->where('wallets.type', 'commission');
            });

        try {
            DB::table('wallets')->insertUsing(['user_id', 'type', 'balance', 'created_at', 'updated_at'], $select);
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function down(): void
    {
        // Best-effort rollback: drop commission wallets and the type column, restore unique(user_id)
        try {
            DB::table('wallets')->where('type', 'commission')->delete();
        } catch (\Throwable $e) {
            // ignore
        }

        Schema::table('wallets', function (Blueprint $table) {
            try {
                $table->dropForeign(['user_id']);
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                $table->dropUnique(['user_id', 'type']);
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                $table->unique(['user_id']);
            } catch (\Throwable $e) {
                // ignore
            }

            if (Schema::hasColumn('wallets', 'type')) {
                try {
                    $table->dropColumn('type');
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            try {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }
};

