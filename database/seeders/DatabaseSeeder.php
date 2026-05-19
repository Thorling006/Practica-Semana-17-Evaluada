<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario de prueba (opcional)
        User::factory()->create([
            'name' => 'Admin Librería',
            'email' => 'admin@libreria.com',
        ]);

        // Sembrar categorías
        $categorias = [
            [
                'nombre' => 'Ficción',
                'descripcion' => 'Novelas, cuentos y obras de narrativa literaria creativa y artística.',
            ],
            [
                'nombre' => 'Fantasía',
                'descripcion' => 'Libros ambientados en mundos mágicos, con criaturas mitológicas y magia.',
            ],
            [
                'nombre' => 'Ciencia Ficción',
                'descripcion' => 'Obras basadas en especulaciones científicas, viajes en el tiempo o futuros futuristas.',
            ],
            [
                'nombre' => 'Tecnología y Programación',
                'descripcion' => 'Guías de desarrollo, lenguajes de programación, ingeniería de software y computación.',
            ],
            [
                'nombre' => 'Historia',
                'descripcion' => 'Libros que relatan y analizan acontecimientos históricos del pasado de la humanidad.',
            ],
            [
                'nombre' => 'Desarrollo Personal',
                'descripcion' => 'Libros orientados al crecimiento personal, liderazgo, finanzas personales y bienestar mental.',
            ]
        ];

        foreach ($categorias as $cat) {
            Category::create($cat);
        }
    }
}
