<?php

namespace App\Console\Commands;

use App\Models\PartnershipPackage;
use App\Models\PartnershipPosition;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PartnershipBuyPosition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partnership:buy {email : User email} {level : QBP level 1-6}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Buy a partnership node position (QBP 1-6), enforcing holder limits.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $level = (int) $this->argument('level');

        if ($level < 1 || $level > 6) {
            $this->error('Level must be between 1 and 6.');
            return Command::INVALID;
        }

        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();
        if (!$user) {
            $this->error('User not found.');
            return Command::FAILURE;
        }

        /** @var PartnershipPackage|null $pkg */
        $pkg = PartnershipPackage::query()->where('level', $level)->where('is_active', true)->first();
        if (!$pkg) {
            $this->error('Partnership package not found/active.');
            return Command::FAILURE;
        }

        try {
            DB::transaction(function () use ($user, $pkg): void {
            $alreadyHasAny = PartnershipPosition::query()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->exists();

            if ($alreadyHasAny) {
                throw new \RuntimeException('User already has an active QBP position.');
            }

            $activeCount = PartnershipPosition::query()
                ->where('partnership_package_id', $pkg->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->count();

            if ($activeCount >= $pkg->holder_limit) {
                throw new \RuntimeException('Holder limit reached for '.$pkg->code);
            }

            PartnershipPosition::firstOrCreate(
                ['user_id' => $user->id, 'partnership_package_id' => $pkg->id],
                ['status' => 'active', 'purchased_at' => Carbon::now()],
            );
            });
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        $this->info('Purchased '.$pkg->code.' for '.$user->email);
        return Command::SUCCESS;
    }
}
