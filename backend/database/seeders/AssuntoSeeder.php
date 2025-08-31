<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Assunto;

class AssuntoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assuntos = [
            ['codAs' => 1, 'Descricao' => 'Romance'],
            ['codAs' => 2, 'Descricao' => 'Ficção'],
            ['codAs' => 3, 'Descricao' => 'Literatura'],
            ['codAs' => 4, 'Descricao' => 'Drama'],
            ['codAs' => 5, 'Descricao' => 'Crônica'],
            ['codAs' => 6, 'Descricao' => 'Poesia'],
            ['codAs' => 7, 'Descricao' => 'Infantil'],
            ['codAs' => 8, 'Descricao' => 'História'],
            ['codAs' => 9, 'Descricao' => 'Biografia'],
            ['codAs' => 10, 'Descricao' => 'Ensaio'],
        ];

        foreach ($assuntos as $assunto) {
            Assunto::create($assunto);
        }
    }
}
