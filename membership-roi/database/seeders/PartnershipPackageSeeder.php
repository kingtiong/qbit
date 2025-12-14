<?php

namespace Database\Seeders;

use App\Models\PartnershipPackage;
use Illuminate\Database\Seeder;

class PartnershipPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            [
                'level' => 1,
                'code' => 'QBP1',
                'currency' => 'USDT',
                'amount' => 1000,
                'holder_limit' => 300,
                'group_percent' => 0.02,
                'global_denom' => 665,
            ],
            [
                'level' => 2,
                'code' => 'QBP2',
                'currency' => 'USDT',
                'amount' => 3000,
                'holder_limit' => 150,
                'group_percent' => 0.03,
                'global_denom' => 365,
            ],
            [
                'level' => 3,
                'code' => 'QBP3',
                'currency' => 'USDT',
                'amount' => 5000,
                'holder_limit' => 100,
                'group_percent' => 0.04,
                'global_denom' => 215,
            ],
            [
                'level' => 4,
                'code' => 'QBP4',
                'currency' => 'USDT',
                'amount' => 10000,
                'holder_limit' => 60,
                'group_percent' => 0.05,
                'global_denom' => 115,
            ],
            [
                'level' => 5,
                'code' => 'QBP5',
                'currency' => 'USDT',
                'amount' => 30000,
                'holder_limit' => 35,
                'group_percent' => 0.06,
                'global_denom' => 55,
            ],
            [
                'level' => 6,
                'code' => 'QBP6',
                'currency' => 'USDT',
                'amount' => 50000,
                'holder_limit' => 20,
                'group_percent' => 0.07,
                'global_denom' => 20,
            ],
        ];

        foreach ($levels as $lvl) {
            PartnershipPackage::updateOrCreate(
                ['level' => $lvl['level']],
                [
                    'code' => $lvl['code'],
                    'currency' => $lvl['currency'],
                    'amount' => $lvl['amount'],
                    'holder_limit' => $lvl['holder_limit'],
                    'group_percent' => $lvl['group_percent'],
                    'global_denom' => $lvl['global_denom'],
                    'is_active' => true,
                ],
            );
        }
    }
}
