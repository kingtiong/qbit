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
        $amounts = [100, 500, 1000, 5000, 10000, 30000];

        foreach ($amounts as $amount) {
            InvestmentPackage::updateOrCreate(
                ['currency' => 'USD', 'amount' => $amount],
                ['label' => "Package {$amount}", 'is_active' => true],
            );
        }
    }
}
