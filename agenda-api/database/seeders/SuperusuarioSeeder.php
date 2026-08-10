<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperusuarioSeeder extends Seeder
{
    public function run()
    {
        DB::table('usuarios')->insert([
            'cedula' => 'admin',
            'nombre' => 'Administrador',
            'correo' => 'admin@agenda.com',
            'password' => Hash::make('admin'),
            'rol' => 'superusuario',
            'is_active' => 1,
            'primer_login' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
