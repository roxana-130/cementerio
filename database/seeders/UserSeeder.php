<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Credenciales de desarrollo iniciales:
     * Email: admin@cementerio.test
     * Password: password
     * Rol: administrador
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@cementerio.test'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'rol' => 'administrador',
                'activo' => true,
            ]
        );
    }
}
