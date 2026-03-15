<?php

namespace Database\Seeders;

use App\Models\Part;
use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class PartSeeder extends Seeder
{
    public function run(): void
    {
        $gst18 = TaxRate::where('percentage', 18)->first();
        $gst5  = TaxRate::where('percentage', 5)->first();
        $defaultTax = $gst18 ? $gst18->id : null;
        $lowTax     = $gst5 ? $gst5->id : null;

        $parts = [
            ['name' => 'Samsung Galaxy S21 Screen', 'sku' => 'PRT-SS21-SCR', 'cost_price' => 1200, 'selling_price' => 1800, 'hsn_code' => '9001', 'tax_rate_id' => $defaultTax],
            ['name' => 'Samsung Galaxy S21 Battery', 'sku' => 'PRT-SS21-BAT', 'cost_price' => 450, 'selling_price' => 700, 'hsn_code' => '8507', 'tax_rate_id' => $defaultTax],
            ['name' => 'iPhone 12 Screen', 'sku' => 'PRT-IP12-SCR', 'cost_price' => 2500, 'selling_price' => 3500, 'hsn_code' => '9001', 'tax_rate_id' => $defaultTax],
            ['name' => 'iPhone 12 Battery', 'sku' => 'PRT-IP12-BAT', 'cost_price' => 800, 'selling_price' => 1200, 'hsn_code' => '8507', 'tax_rate_id' => $defaultTax],
            ['name' => 'Samsung A54 Screen', 'sku' => 'PRT-SA54-SCR', 'cost_price' => 900, 'selling_price' => 1400, 'hsn_code' => '9001', 'tax_rate_id' => $defaultTax],
            ['name' => 'OnePlus Nord Charging Port', 'sku' => 'PRT-OPN-CHP', 'cost_price' => 200, 'selling_price' => 400, 'hsn_code' => '8544', 'tax_rate_id' => $defaultTax],
            ['name' => 'Universal SIM Tray', 'sku' => 'PRT-UNI-SIM', 'cost_price' => 30, 'selling_price' => 80, 'hsn_code' => '8517', 'tax_rate_id' => $lowTax],
            ['name' => 'iPhone 13 Back Glass', 'sku' => 'PRT-IP13-BGL', 'cost_price' => 600, 'selling_price' => 1000, 'hsn_code' => '9001', 'tax_rate_id' => $defaultTax],
            ['name' => 'Samsung S21 Charging Flex', 'sku' => 'PRT-SS21-CFX', 'cost_price' => 350, 'selling_price' => 600, 'hsn_code' => '8544', 'tax_rate_id' => $defaultTax],
            ['name' => 'Xiaomi Redmi Note 11 Screen', 'sku' => 'PRT-XRN11-SCR', 'cost_price' => 700, 'selling_price' => 1100, 'hsn_code' => '9001', 'tax_rate_id' => $defaultTax],
            ['name' => 'Universal Earpiece Speaker', 'sku' => 'PRT-UNI-EAR', 'cost_price' => 80, 'selling_price' => 200, 'hsn_code' => '8518', 'tax_rate_id' => $defaultTax],
            ['name' => 'iPhone 12 Charging Port', 'sku' => 'PRT-IP12-CHP', 'cost_price' => 500, 'selling_price' => 900, 'hsn_code' => '8544', 'tax_rate_id' => $defaultTax],
        ];

        foreach ($parts as $p) {
            Part::firstOrCreate(['sku' => $p['sku']], $p);
        }
    }
}
