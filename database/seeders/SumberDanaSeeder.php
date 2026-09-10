<?php

namespace Database\Seeders;

use App\Models\SumberDana;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SumberDanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sumberDana = [
            'APBN',
            'APBD Provinsi',
            'APBD Kabupaten',
            'Swadaya',
        ];

        foreach ($sumberDana as $nama) {
            SumberDana::create(['nama_sumber' => $nama]);
        }
    }
}