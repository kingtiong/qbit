<?php

namespace Database\Seeders;

use App\Models\InvestmentPackage;
use Illuminate\Database\Seeder;

class InvestmentPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'code' => 'QPU-NANO',
                'label' => 'QPU - NANO',
                'summary' => 'Entry QPU package with fixed daily QOS and 1.5x cap.',
                'benefits' => [
                    'Daily QOS: 0.30 USDT',
                    'Max return: 1.5x of investment (includes QOS + sponsor + network rewards)',
                    'Multiple purchases allowed',
                ],
                'currency' => 'USDT',
                'amount' => 100,
                'daily_qos_amount' => 0.30,
                'max_return_multiplier' => 1.50,
            ],
            [
                'code' => 'QPU-MICRO',
                'label' => 'QPU - MICRO',
                'summary' => 'Micro tier with higher daily QOS and 2x cap.',
                'benefits' => [
                    'Daily QOS: 5.00 USDT',
                    'Max return: 2x of investment',
                ],
                'currency' => 'USDT',
                'amount' => 1000,
                'daily_qos_amount' => 5.00,
                'max_return_multiplier' => 2.00,
            ],
            [
                'code' => 'QPU-CORE',
                'label' => 'QPU - CORE',
                'summary' => 'Core tier with 3x cap.',
                'benefits' => [
                    'Daily QOS: 30.00 USDT',
                    'Max return: 3x of investment',
                ],
                'currency' => 'USDT',
                'amount' => 5000,
                'daily_qos_amount' => 30.00,
                'max_return_multiplier' => 3.00,
            ],
            [
                'code' => 'QPU-FUSION',
                'label' => 'QPU - FUSION',
                'summary' => 'Fusion tier with 3x cap.',
                'benefits' => [
                    'Daily QOS: 70.00 USDT',
                    'Max return: 3x of investment',
                ],
                'currency' => 'USDT',
                'amount' => 10000,
                'daily_qos_amount' => 70.00,
                'max_return_multiplier' => 3.00,
            ],
            [
                'code' => 'QPU-X',
                'label' => 'QPU - X',
                'summary' => 'Top tier with 3.5x cap.',
                'benefits' => [
                    'Daily QOS: 400.00 USDT',
                    'Max return: 3.5x of investment',
                ],
                'currency' => 'USDT',
                'amount' => 50000,
                'daily_qos_amount' => 400.00,
                'max_return_multiplier' => 3.50,
            ],
        ];

        foreach ($packages as $p) {
            InvestmentPackage::updateOrCreate(
                ['code' => $p['code']],
                [
                    'label' => $p['label'],
                    'summary' => $p['summary'] ?? null,
                    'benefits' => $p['benefits'] ?? null,
                    'currency' => $p['currency'],
                    'amount' => $p['amount'],
                    'daily_qos_amount' => $p['daily_qos_amount'],
                    'max_return_multiplier' => $p['max_return_multiplier'],
                    'is_active' => true,
                ],
            );
        }
    }
}
