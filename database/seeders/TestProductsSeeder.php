<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skus = [
            'ASDF-1123',
            'ASDF-1124',
            'ASDF-1125',
            'ASDF-1126',
            'ASDF-1127',
            'ASDF-1128',
            'ASDF-1129',
            'ASDF-1130',
            'ASDF-1131',
            'ASDF-1132',
            'ASDF-1133',
            'ASDF-1134',
            'ASDF-1135',
            'ASDF-1136',
            'ASDF-1137',
            'ASDF-1138',
            'ASDF-1139',
            'ASDF-1140',
            'ASDF-1141',
            'ASDF-1142',
            'ASDF-1143',
            'ASDF-1144',
        ];

        foreach ($skus as $sku) {
            DB::table('products')->updateOrInsert(
                ['sku' => $sku],
                [
                    'name' => "Product {$sku}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
