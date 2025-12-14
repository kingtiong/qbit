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
                'currency' => 'USDT',
                'amount' => 100,
                'daily_qos_amount' => 0.30,
                'max_return_multiplier' => 1.50,
            ],
            [
                'code' => 'QPU-MICRO',
                'label' => 'QPU - MICRO',
                'currency' => 'USDT',
                'amount' => 1000,
                'daily_qos_amount' => 5.00,
                'max_return_multiplier' => 2.00,
            ],
            [
                'code' => 'QPU-CORE',
                'label' => 'QPU - CORE',
                'currency' => 'USDT',
                'amount' => 5000,
                'daily_qos_amount' => 30.00,
                'max_return_multiplier' => 3.00,
            ],
            [
                'code' => 'QPU-FUSION',
                'label' => 'QPU - FUSION',
                'currency' => 'USDT',
                'amount' => 10000,
                'daily_qos_amount' => 70.00,
                'max_return_multiplier' => 3.00,
            ],
            [
                'code' => 'QPU-X',
                'label' => 'QPU - X',
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
