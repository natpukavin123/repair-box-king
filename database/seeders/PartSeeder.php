<?php

namespace Database\Seeders;

use App\Models\Part;
use Illuminate\Database\Seeder;

class PartSeeder extends Seeder
{
    public function run(): void
    {
        $parts = [
            ['name' => 'Samsung Galaxy S21 Screen', 'sku' => 'PRT-SS21-SCR', 'cost_price' => 1200, 'selling_price' => 1800],
            ['name' => 'Samsung Galaxy S21 Battery', 'sku' => 'PRT-SS21-BAT', 'cost_price' => 450, 'selling_price' => 700],
            ['name' => 'iPhone 12 Screen', 'sku' => 'PRT-IP12-SCR', 'cost_price' => 2500, 'selling_price' => 3500],
            ['name' => 'iPhone 12 Battery', 'sku' => 'PRT-IP12-BAT', 'cost_price' => 800, 'selling_price' => 1200],
            ['name' => 'Samsung A54 Screen', 'sku' => 'PRT-SA54-SCR', 'cost_price' => 900, 'selling_price' => 1400],
            ['name' => 'OnePlus Nord Charging Port', 'sku' => 'PRT-OPN-CHP', 'cost_price' => 200, 'selling_price' => 400],
            ['name' => 'Universal SIM Tray', 'sku' => 'PRT-UNI-SIM', 'cost_price' => 30, 'selling_price' => 80],
            ['name' => 'iPhone 13 Back Glass', 'sku' => 'PRT-IP13-BGL', 'cost_price' => 600, 'selling_price' => 1000],
            ['name' => 'Samsung S21 Charging Flex', 'sku' => 'PRT-SS21-CFX', 'cost_price' => 350, 'selling_price' => 600],
            ['name' => 'Xiaomi Redmi Note 11 Screen', 'sku' => 'PRT-XRN11-SCR', 'cost_price' => 700, 'selling_price' => 1100],
            ['name' => 'Universal Earpiece Speaker', 'sku' => 'PRT-UNI-EAR', 'cost_price' => 80, 'selling_price' => 200],
            ['name' => 'iPhone 12 Charging Port', 'sku' => 'PRT-IP12-CHP', 'cost_price' => 500, 'selling_price' => 900],
        ];

        foreach ($parts as $p) {
            Part::firstOrCreate(['sku' => $p['sku']], $p);
        }
    }
}
