<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Opd;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get OPDs
        $pemkotBlitar = Opd::where('nama_penyelenggara', 'Pemkot Blitar')->first();
        $penyelenggaraA = Opd::where('nama_penyelenggara', 'Penyelenggara A')->first();
        $penyelenggaraB = Opd::where('nama_penyelenggara', 'Penyelenggara B')->first();

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@mail.com',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'opd_id' => $pemkotBlitar ? $pemkotBlitar->id : null,
            ],
            [
                'name' => 'Developer',
                'email' => 'developer@mail.com',
                'password' => Hash::make('password'),
                'role' => 'developer',
                'opd_id' => $pemkotBlitar ? $pemkotBlitar->id : null,
            ],
            [
                'name' => 'Penyelenggara A',
                'email' => 'penyelenggara.a@mail.com',
                'password' => Hash::make('password'),
                'role' => 'admin_penyelenggara',
                'opd_id' => $penyelenggaraA ? $penyelenggaraA->id : null,
            ],
            [
                'name' => 'Penyelenggara B',
                'email' => 'penyelenggara.b@mail.com',
                'password' => Hash::make('password'),
                'role' => 'admin_penyelenggara',
                'opd_id' => $penyelenggaraB ? $penyelenggaraB->id : null,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
