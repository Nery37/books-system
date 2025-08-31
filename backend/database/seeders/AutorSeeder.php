<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Autor;

class AutorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $autores = [
            ['CodAu' => 1, 'Nome' => 'Machado de Assis'],
            ['CodAu' => 2, 'Nome' => 'Clarice Lispector'],
            ['CodAu' => 3, 'Nome' => 'José de Alencar'],
            ['CodAu' => 4, 'Nome' => 'Rachel de Queiroz'],
            ['CodAu' => 5, 'Nome' => 'Jorge Amado'],
            ['CodAu' => 6, 'Nome' => 'Graciliano Ramos'],
            ['CodAu' => 7, 'Nome' => 'Monteiro Lobato'],
            ['CodAu' => 8, 'Nome' => 'Érico Veríssimo'],
            ['CodAu' => 9, 'Nome' => 'Cecília Meireles'],
            ['CodAu' => 10, 'Nome' => 'Carlos Drummond de Andrade'],
        ];

        foreach ($autores as $autor) {
            Autor::create($autor);
        }
    }
}
