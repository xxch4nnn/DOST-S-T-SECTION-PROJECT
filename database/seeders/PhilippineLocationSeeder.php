<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\Region;
use Illuminate\Database\Seeder;

class PhilippineLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Regions
        $jsonPath = storage_path('app/json/table_region.json');
        $regions = json_decode(file_get_contents($jsonPath), true);
        $regionRows = array_map(function ($item) {
            return [
                'id' => $item['region_id'],
                'name' => $item['region_description'],
                'abbreviation' => $item['region_name'],
                'is_available' => true,
            ];
        }, $regions);

        foreach (array_chunk($regionRows, 1000) as $chunk) {
            Region::insert($chunk);
        }

        // Provinces
        $jsonPath = storage_path('app/json/table_province.json');
        $provinces = json_decode(file_get_contents($jsonPath), true);
        $provinceRows = array_map(function ($item) {
            return [
                'id' => $item['province_id'],
                'region_id' => $item['region_id'],
                'name' => $item['province_name'],
            ];
        }, $provinces);

        foreach (array_chunk($provinceRows, 1000) as $chunk) {
            Province::insert($chunk);
        }

        // Municipalities
        $jsonPath = storage_path('app/json/table_municipality.json');
        $municipalities = json_decode(file_get_contents($jsonPath), true);
        $municipalityRows = array_map(function ($item) {
            return [
                'id' => $item['municipality_id'],
                'province_id' => $item['province_id'],
                'name' => $item['municipality_name'],
            ];
        }, $municipalities);

        foreach (array_chunk($municipalityRows, 1000) as $chunk) {
            Municipality::insert($chunk);
        }

        // Barangays
        $jsonPath = storage_path('app/json/table_barangay.json');
        $barangays = json_decode(file_get_contents($jsonPath), true);
        $barangayRows = array_map(function ($item) {
            return [
                'id' => $item['barangay_id'],
                'municipality_id' => $item['municipality_id'],
                'name' => $item['barangay_name'],
            ];
        }, $barangays);

        foreach (array_chunk($barangayRows, 1000) as $chunk) {
            Barangay::insert($chunk);
        }

        // Street

        // Block and Address
    }

    // municipality_id, barangay_id
}
