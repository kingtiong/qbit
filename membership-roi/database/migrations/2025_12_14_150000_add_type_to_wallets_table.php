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
        // IMPORTANT: Schema builder "try/catch" doesn't reliably catch DDL errors because the SQL
        // is executed after the closure finishes. Use information_schema checks + raw statements.
        try {
            $dbName = DB::getDatabaseName();

            // Drop legacy unique index if it exists (may already be gone from a previous failed run).
            $legacy = DB::select(
                "SELECT 1
                 FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = ?
                   AND TABLE_NAME = 'wallets'
                   AND INDEX_NAME = 'wallets_user_id_unique'
                 LIMIT 1",
                [$dbName]
            );
            if (!empty($legacy)) {
                DB::statement("ALTER TABLE `wallets` DROP INDEX `wallets_user_id_unique`");
            }

            // Ensure a UNIQUE index exists on (user_id, type).
            $uniqueIndexes = DB::select(
                "SELECT INDEX_NAME,
                        GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS cols
                 FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = ?
                   AND TABLE_NAME = 'wallets'
                   AND NON_UNIQUE = 0
                 GROUP BY INDEX_NAME",
                [$dbName]
            );

            $hasUserTypeUnique = false;
            foreach ($uniqueIndexes as $idx) {
                $cols = (string) ($idx->cols ?? '');
                if ($cols === 'user_id,type' || $cols === 'type,user_id') {
                    $hasUserTypeUnique = true;
                    break;
                }
            }

            if (!$hasUserTypeUnique) {
                DB::statement("ALTER TABLE `wallets` ADD UNIQUE INDEX `wallets_user_id_type_unique` (`user_id`, `type`)");
            }
        } catch (\Throwable $e) {
            // ignore
        }

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
            // We handle index changes below via raw SQL checks.

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

        // Restore legacy unique(user_id) index if needed; drop unique(user_id,type) if present.
        try {
            $dbName = DB::getDatabaseName();

            $idxRows = DB::select(
                "SELECT INDEX_NAME,
                        GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS cols,
                        MAX(NON_UNIQUE) AS non_unique
                 FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = ?
                   AND TABLE_NAME = 'wallets'
                 GROUP BY INDEX_NAME",
                [$dbName]
            );

            foreach ($idxRows as $idx) {
                $cols = (string) ($idx->cols ?? '');
                $isUnique = ((int) ($idx->non_unique ?? 1)) === 0;
                $name = (string) ($idx->INDEX_NAME ?? '');
                if ($isUnique && ($cols === 'user_id,type' || $cols === 'type,user_id')) {
                    DB::statement("ALTER TABLE `wallets` DROP INDEX `{$name}`");
                }
            }

            $legacy = DB::select(
                "SELECT 1
                 FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = ?
                   AND TABLE_NAME = 'wallets'
                   AND INDEX_NAME = 'wallets_user_id_unique'
                 LIMIT 1",
                [$dbName]
            );
            if (empty($legacy)) {
                DB::statement("ALTER TABLE `wallets` ADD UNIQUE INDEX `wallets_user_id_unique` (`user_id`)");
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }
};

