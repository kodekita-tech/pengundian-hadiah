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
        $opds = [
            [
                'nama_penyelenggara' => 'Pemkot Blitar',
                'singkatan' => 'Pemkot Blitar',
                'nomor_hp' => null,
            ],
            [
                'nama_penyelenggara' => 'Penyelenggara A',
                'singkatan' => 'Penyelenggara A',
                'nomor_hp' => null,
            ],
            [
                'nama_penyelenggara' => 'Penyelenggara B',
                'singkatan' => 'Penyelenggara B',
                'nomor_hp' => null,
            ],
        ];

        foreach ($opds as $opd) {
            Opd::updateOrCreate(
                ['nama_penyelenggara' => $opd['nama_penyelenggara']],
                $opd
            );
        }
    }
}

