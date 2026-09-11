<?php

namespace Database\Seeders;

use App\Modules\Usuarios\Models\Usuario;
use App\Support\Enums\RolEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Usuario::create([
            'username' => 'admin',
            'nombre' => 'Administrador',
            'paterno' => 'General',
            'email' => 'admin@acb.local',
            'password' => Hash::make('admin123'),
            'rol' => RolEnum::ADMIN,
            'puede_controlar_tiempo' => true,
            'email_verified_at' => now(),
        ]);
    }
}