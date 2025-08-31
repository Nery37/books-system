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

    public function gerarRelatorioPDF()
    {
        $dados = $this->getRelatorioLivrosPorAutor();
        
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = view('relatorios.livros-por-autor', compact('dados'))->render();
        
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
                ->select('autor_nome', DB::raw('COUNT(*) as total_livros'))
                ->groupBy('autor_nome')
                ->orderBy('total_livros', 'desc')
                ->limit(10)
                ->get()
        ];
    }
}
