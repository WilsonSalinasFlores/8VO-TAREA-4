<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoPreguntasSeeder extends Seeder
{
    public function run()
    {
        $preguntas = [
            '¿Cuál es el nombre de tu primera mascota?',
            '¿En qué ciudad naciste?',
            '¿Cuál es el apellido de soltera de tu madre?',
            '¿Cuál fue el nombre de tu primera escuela?',
            '¿Cuál es tu comida favorita?',
            '¿Cuál es el nombre de tu mejor amigo de la infancia?',
            '¿Cuál es tu película favorita?',
            '¿Cuál fue el modelo de tu primer vehículo?',
            '¿Cuál es el nombre de tu libro favorito?',
            '¿Cuál es tu equipo deportivo favorito?',
        ];

        foreach ($preguntas as $pregunta) {
            DB::table('catalogo_preguntas')->insert([
                'pregunta' => $pregunta,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
