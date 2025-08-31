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
            // Livros existentes
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
            
            // Novos livros - Machado de Assis (ID: 1)
            [
                'Codl' => 6,
                'Titulo' => 'O Alienista',
                'Editora' => 'Ática',
                'Edicao' => 2,
                'AnoPublicacao' => '1882',
                'Valor' => 32.50,
                'autores' => [1],
                'assuntos' => [1, 2]
            ],
            [
                'Codl' => 7,
                'Titulo' => 'Memórias Póstumas de Brás Cubas',
                'Editora' => 'Moderna',
                'Edicao' => 1,
                'AnoPublicacao' => '1881',
                'Valor' => 48.90,
                'autores' => [1],
                'assuntos' => [1, 3]
            ],
            [
                'Codl' => 8,
                'Titulo' => 'Quincas Borba',
                'Editora' => 'FTD',
                'Edicao' => 1,
                'AnoPublicacao' => '1891',
                'Valor' => 41.20,
                'autores' => [1],
                'assuntos' => [1, 3]
            ],
            
            // Clarice Lispector (ID: 2)
            [
                'Codl' => 9,
                'Titulo' => 'Água Viva',
                'Editora' => 'Rocco',
                'Edicao' => 1,
                'AnoPublicacao' => '1973',
                'Valor' => 35.90,
                'autores' => [2],
                'assuntos' => [1, 2]
            ],
            [
                'Codl' => 10,
                'Titulo' => 'A Paixão Segundo G.H.',
                'Editora' => 'Rocco',
                'Edicao' => 2,
                'AnoPublicacao' => '1964',
                'Valor' => 39.90,
                'autores' => [2],
                'assuntos' => [1, 2]
            ],
            [
                'Codl' => 11,
                'Titulo' => 'Perto do Coração Selvagem',
                'Editora' => 'Rocco',
                'Edicao' => 1,
                'AnoPublicacao' => '1943',
                'Valor' => 44.50,
                'autores' => [2],
                'assuntos' => [1, 2]
            ],
            
            // José de Alencar (ID: 3)
            [
                'Codl' => 12,
                'Titulo' => 'Iracema',
                'Editora' => 'Ática',
                'Edicao' => 3,
                'AnoPublicacao' => '1865',
                'Valor' => 33.90,
                'autores' => [3],
                'assuntos' => [1, 3]
            ],
            [
                'Codl' => 13,
                'Titulo' => 'O Guarani',
                'Editora' => 'Moderna',
                'Edicao' => 2,
                'AnoPublicacao' => '1857',
                'Valor' => 37.80,
                'autores' => [3],
                'assuntos' => [1, 3]
            ],
            [
                'Codl' => 14,
                'Titulo' => 'Senhora',
                'Editora' => 'FTD',
                'Edicao' => 1,
                'AnoPublicacao' => '1875',
                'Valor' => 36.50,
                'autores' => [3],
                'assuntos' => [1, 3]
            ],
            
            // Rachel de Queiroz (ID: 4)
            [
                'Codl' => 15,
                'Titulo' => 'Dôra, Doralina',
                'Editora' => 'José Olympio',
                'Edicao' => 2,
                'AnoPublicacao' => '1975',
                'Valor' => 43.20,
                'autores' => [4],
                'assuntos' => [1, 2]
            ],
            [
                'Codl' => 16,
                'Titulo' => 'Memorial de Maria Moura',
                'Editora' => 'Siciliano',
                'Edicao' => 1,
                'AnoPublicacao' => '1992',
                'Valor' => 52.90,
                'autores' => [4],
                'assuntos' => [1, 3]
            ],
            [
                'Codl' => 17,
                'Titulo' => 'As Três Marias',
                'Editora' => 'José Olympio',
                'Edicao' => 3,
                'AnoPublicacao' => '1939',
                'Valor' => 38.70,
                'autores' => [4],
                'assuntos' => [1, 2]
            ],
            
            // Jorge Amado (ID: 5)
            [
                'Codl' => 18,
                'Titulo' => 'Dona Flor e Seus Dois Maridos',
                'Editora' => 'Companhia das Letras',
                'Edicao' => 1,
                'AnoPublicacao' => '1966',
                'Valor' => 46.90,
                'autores' => [5],
                'assuntos' => [1, 2]
            ],
            [
                'Codl' => 19,
                'Titulo' => 'Gabriela, Cravo e Canela',
                'Editora' => 'Companhia das Letras',
                'Edicao' => 2,
                'AnoPublicacao' => '1958',
                'Valor' => 49.90,
                'autores' => [5],
                'assuntos' => [1, 3]
            ],
            [
                'Codl' => 20,
                'Titulo' => 'Tieta do Agreste',
                'Editora' => 'Record',
                'Edicao' => 1,
                'AnoPublicacao' => '1977',
                'Valor' => 44.80,
                'autores' => [5],
                'assuntos' => [1, 2]
            ],
            
            // Livros adicionais com múltiplos autores
            [
                'Codl' => 21,
                'Titulo' => 'Antologia Poética Brasileira',
                'Editora' => 'Nova Fronteira',
                'Edicao' => 1,
                'AnoPublicacao' => '2000',
                'Valor' => 55.90,
                'autores' => [1, 2, 3],
                'assuntos' => [2, 3]
            ],
            [
                'Codl' => 22,
                'Titulo' => 'Contos Modernos',
                'Editora' => 'Globo',
                'Edicao' => 1,
                'AnoPublicacao' => '1985',
                'Valor' => 34.90,
                'autores' => [2, 4],
                'assuntos' => [1, 2]
            ],
            [
                'Codl' => 23,
                'Titulo' => 'Clássicos da Literatura',
                'Editora' => 'Saraiva',
                'Edicao' => 2,
                'AnoPublicacao' => '1995',
                'Valor' => 67.50,
                'autores' => [1, 3, 5],
                'assuntos' => [1, 2, 3]
            ],
            [
                'Codl' => 24,
                'Titulo' => 'Nordeste em Versos',
                'Editora' => 'Martins Fontes',
                'Edicao' => 1,
                'AnoPublicacao' => '1988',
                'Valor' => 41.60,
                'autores' => [4, 5],
                'assuntos' => [2, 3]
            ],
            [
                'Codl' => 25,
                'Titulo' => 'Realismo Brasileiro',
                'Editora' => 'Ática',
                'Edicao' => 3,
                'AnoPublicacao' => '1990',
                'Valor' => 58.90,
                'autores' => [1, 3],
                'assuntos' => [1, 3]
            ],
        ];

        foreach ($livros as $livroData) {
            $autores = $livroData['autores'];
            $assuntos = $livroData['assuntos'];
            
            unset($livroData['autores'], $livroData['assuntos']);
            
            // Usar updateOrCreate para evitar duplicatas
            $livro = Livro::updateOrCreate(
                ['Codl' => $livroData['Codl']], // Condição de busca
                $livroData // Dados para criar/atualizar
            );
            
            // Sincronizar relações (remove antigas e adiciona novas)
            $livro->autores()->sync($autores);
            $livro->assuntos()->sync($assuntos);
        }
    }
}
