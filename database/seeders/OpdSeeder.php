<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opd = Opd::updateOrCreate(
            ['nama_instansi' => 'Pemkot Blitar'],
            [
                'nama_instansi' => 'Pemkot Blitar',
                'singkatan' => 'Pemkot Blitar',
                'nomor_hp' => null,
            ]
        );
    }
}

