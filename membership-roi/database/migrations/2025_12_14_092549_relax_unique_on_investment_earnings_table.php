<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // New installs no longer create the unique index.
        // This migration exists only to safely relax older deployments.
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        $row = DB::selectOne("
            SELECT COUNT(1) AS c
            FROM information_schema.STATISTICS
            WHERE table_schema = DATABASE()
              AND table_name = 'investment_earnings'
              AND index_name = 'investment_earnings_investment_id_date_source_unique'
        ");

        if (!$row || (int) $row->c === 0) {
            return;
        }

        // If the foreign key is using this composite index, drop FK first, then replace index, then re-add FK.
        DB::statement("ALTER TABLE `investment_earnings` DROP FOREIGN KEY `investment_earnings_investment_id_foreign`");
        DB::statement("ALTER TABLE `investment_earnings` DROP INDEX `investment_earnings_investment_id_date_source_unique`");
        DB::statement("CREATE INDEX `investment_earnings_investment_id_date_source_index` ON `investment_earnings` (`investment_id`, `date`, `source`)");
        DB::statement("
            ALTER TABLE `investment_earnings`
            ADD CONSTRAINT `investment_earnings_investment_id_foreign`
            FOREIGN KEY (`investment_id`) REFERENCES `investments` (`id`)
            ON DELETE CASCADE
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op (we don't restore uniqueness).
    }
};
