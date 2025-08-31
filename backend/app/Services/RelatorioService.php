<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;

class RelatorioService
{
    public function getRelatorioLivrosPorAutor()
    {
        return DB::table('vw_relatorio_livros')
            ->orderBy('autor_nome')
            ->orderBy('livro_titulo')
            ->get()
            ->groupBy('autor_nome');
    }

    public function getRelatorioLivrosPorAutorFormatado()
    {
        // Buscar todos os livros únicos primeiro
        $livros = DB::table('livros')
            ->join('livro_autor', 'livros.Codl', '=', 'livro_autor.Livro_Codl')
            ->join('autores', 'livro_autor.Autor_CodAu', '=', 'autores.CodAu')
            ->select('livros.*', 'autores.Nome as autor_nome')
            ->orderBy('autores.Nome')
            ->orderBy('livros.Titulo')
            ->get();
        
        // Agrupar por autor
        $livrosPorAutor = $livros->groupBy('autor_nome');
        $resultado = [];
        
        foreach ($livrosPorAutor as $autorNome => $livrosDoAutor) {
            $livrosUnicos = $livrosDoAutor->unique('Codl'); // Remove duplicatas por ID do livro
            
            $valorTotal = $livrosUnicos->sum('Valor');
            
            $resultado[] = [
                'autor' => $autorNome,
                'total_livros' => $livrosUnicos->count(),
                'livros' => $livrosUnicos->map(function($livro) {
                    return [
                        'titulo' => $livro->Titulo,
                        'valor' => $livro->Valor,
                        'valor_formatado' => 'R$ ' . number_format($livro->Valor, 2, ',', '.')
                    ];
                })->values()->toArray(),
                'valor_total' => $valorTotal,
                'valor_total_formatado' => 'R$ ' . number_format($valorTotal, 2, ',', '.')
            ];
        }
        
        return collect($resultado);
    }

    public function getEstatisticasRelatorio()
    {
        $relatorio = $this->getRelatorioLivrosPorAutorFormatado();
        
        // Para calcular o total geral correto, precisamos contar cada livro apenas uma vez
        $todosOsLivros = collect();
        foreach ($relatorio as $autorData) {
            foreach ($autorData['livros'] as $livro) {
                $key = $livro['titulo'] . '|' . $livro['valor'];
                if (!$todosOsLivros->contains('key', $key)) {
                    $todosOsLivros->push([
                        'key' => $key,
                        'valor' => $livro['valor']
                    ]);
                }
            }
        }
        
        return [
            'total_autores' => $relatorio->count(),
            'total_livros' => $todosOsLivros->count(),
            'valor_total' => $todosOsLivros->sum('valor')
        ];
    }

    public function gerarRelatorioPDF()
    {
        $livrosPorAutor = $this->getRelatorioLivrosPorAutor();
        
        // Calcular estatísticas para o relatório
        $totalAutores = $livrosPorAutor->count();
        
        // Para evitar duplicação, vamos coletar apenas livros únicos
        $livrosUnicos = collect();
        foreach ($livrosPorAutor as $autorLivros) {
            foreach ($autorLivros as $livro) {
                if (!$livrosUnicos->contains('livro_id', $livro->livro_id)) {
                    $livrosUnicos->push($livro);
                }
            }
        }
        
        $totalLivros = $livrosUnicos->count();
        $valorTotal = $livrosUnicos->sum('livro_valor');
        $dataGeracao = now()->format('d/m/Y H:i:s');
        
        // Verificar se há filtro de autor específico
        $filtroAutor = null; // Pode ser implementado posteriormente se necessário
        
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = view('relatorios.livros-por-autor', compact(
            'livrosPorAutor',
            'totalAutores',
            'totalLivros', 
            'valorTotal',
            'dataGeracao',
            'filtroAutor'
        ))->render();
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->output();
    }

    public function getEstatisticas()
    {
        return [
            'total_livros' => DB::table('livros')->count(),
            'total_autores' => DB::table('autores')->count(),
            'total_assuntos' => DB::table('assuntos')->count(),
            'valor_total_acervo' => DB::table('livros')->sum('Valor'),
            'ano_mais_recente' => DB::table('livros')->max('AnoPublicacao'),
            'ano_mais_antigo' => DB::table('livros')->min('AnoPublicacao'),
            'livros_por_autor' => DB::table('vw_relatorio_livros')
                ->select('autor_nome', DB::raw('COUNT(DISTINCT livro_id) as total_livros'))
                ->groupBy('autor_nome')
                ->orderBy('total_livros', 'desc')
                ->limit(10)
                ->get()
        ];
    }
}
