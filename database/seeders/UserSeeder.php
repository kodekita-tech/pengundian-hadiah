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
        // Get Pemkot Blitar OPD
        $pemkotBlitar = Opd::where('nama_instansi', 'Pemkot Blitar')->first();

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
                'name' => 'Diskominfotik',
                'email' => 'diskominfotik@blitarkota.go.id',
                'password' => Hash::make('password'),
                'role' => 'admin_opd',
                'opd_id' => null,
            ],
            [
                'name' => 'DP3AP2KB',
                'email' => 'dp3ap2kb@blitarkota.go.id',
                'password' => Hash::make('password'),
                'role' => 'admin_opd',
                'opd_id' => null,
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
