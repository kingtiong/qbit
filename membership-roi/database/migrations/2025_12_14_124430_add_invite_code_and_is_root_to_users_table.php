<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('invite_code', 32)->nullable()->after('email');
            $table->boolean('is_root')->default(false)->after('is_admin');

            $table->unique(['invite_code']);
            $table->index(['is_root']);
        });

        // Backfill existing users so every account has a locked invite code.
        $existingCodes = DB::table('users')
            ->whereNotNull('invite_code')
            ->pluck('invite_code')
            ->map(fn ($c) => strtoupper((string) $c))
            ->all();
        $existingSet = array_fill_keys($existingCodes, true);

        $users = DB::table('users')->select('id', 'invite_code')->get();
        foreach ($users as $u) {
            if (!empty($u->invite_code)) {
                continue;
            }
            do {
                $code = Str::upper(Str::random(10));
            } while (isset($existingSet[$code]));
            $existingSet[$code] = true;
            DB::table('users')->where('id', $u->id)->update(['invite_code' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_root']);
            $table->dropUnique(['invite_code']);
            $table->dropColumn(['invite_code', 'is_root']);
        });
    }
};
