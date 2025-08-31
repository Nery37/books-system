<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Livro;

class LivroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $livros = [
            [
                'Codl' => 1,
                'Titulo' => 'Dom Casmurro',
                'Editora' => 'Ática',
                'Edicao' => 1,
                'AnoPublicacao' => '1899',
                'Valor' => 45.90,
                'autores' => [1],
                'assuntos' => [1, 3]
            ],
            [
                'Codl' => 2,
                'Titulo' => 'O Cortiço',
                'Editora' => 'Scipione',
                'Edicao' => 2,
                'AnoPublicacao' => '1890',
                'Valor' => 35.50,
                'autores' => [3],
                'assuntos' => [1, 3]
            ],
            [
                'Codl' => 3,
                'Titulo' => 'A Hora da Estrela',
                'Editora' => 'Rocco',
                'Edicao' => 1,
                'AnoPublicacao' => '1977',
                'Valor' => 29.90,
                'autores' => [2],
                'assuntos' => [1, 2]
            ],
            [
                'Codl' => 4,
                'Titulo' => 'O Quinze',
                'Editora' => 'José Olympio',
                'Edicao' => 3,
                'AnoPublicacao' => '1930',
                'Valor' => 42.00,
                'autores' => [4],
                'assuntos' => [1, 3]
            ],
            [
                'Codl' => 5,
                'Titulo' => 'Capitães da Areia',
                'Editora' => 'Companhia das Letras',
                'Edicao' => 1,
                'AnoPublicacao' => '1937',
                'Valor' => 38.90,
                'autores' => [5],
                'assuntos' => [1, 3]
            ],
        ];

        foreach ($livros as $livroData) {
            $autores = $livroData['autores'];
            $assuntos = $livroData['assuntos'];
            
            unset($livroData['autores'], $livroData['assuntos']);
            
            $livro = Livro::create($livroData);
            
            // Anexar autores e assuntos
            $livro->autores()->attach($autores);
            $livro->assuntos()->attach($assuntos);
        }
    }
}
