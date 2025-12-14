<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop FK on wallets.user_id if it exists (constraint name may differ per environment).
        try {
            $dbName = DB::getDatabaseName();
            $rows = DB::select(
                "SELECT CONSTRAINT_NAME
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = ?
                   AND TABLE_NAME = 'wallets'
                   AND COLUMN_NAME = 'user_id'
                   AND REFERENCED_TABLE_NAME IS NOT NULL",
                [$dbName]
            );
            $constraint = $rows[0]->CONSTRAINT_NAME ?? null;
            if (is_string($constraint) && $constraint !== '') {
                DB::statement("ALTER TABLE `wallets` DROP FOREIGN KEY `{$constraint}`");
            }
        } catch (\Throwable $e) {
            // ignore
        }

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

        // Drop FK on wallets.user_id if it exists (constraint name may differ per environment).
        try {
            $dbName = DB::getDatabaseName();
            $rows = DB::select(
                "SELECT CONSTRAINT_NAME
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = ?
                   AND TABLE_NAME = 'wallets'
                   AND COLUMN_NAME = 'user_id'
                   AND REFERENCED_TABLE_NAME IS NOT NULL",
                [$dbName]
            );
            $constraint = $rows[0]->CONSTRAINT_NAME ?? null;
            if (is_string($constraint) && $constraint !== '') {
                DB::statement("ALTER TABLE `wallets` DROP FOREIGN KEY `{$constraint}`");
            }
        } catch (\Throwable $e) {
            // ignore
        }

        Schema::table('wallets', function (Blueprint $table) {
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

