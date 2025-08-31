<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\RelatorioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioController extends Controller
{
    protected $relatorioService;

    public function __construct(RelatorioService $relatorioService)
    {
        $this->relatorioService = $relatorioService;
    }

    /**
     * Retorna estatísticas gerais do sistema
     */
    public function estatisticas(): JsonResponse
    {
        try {
            $estatisticas = $this->relatorioService->getEstatisticas();
    
        return response()->json([
                'success' => true,
                'data' => $estatisticas,
                'message' => 'Estatísticas recuperadas com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorna relatóro dtie livros por autor
     */
    public function livrosPorAutor(Request $request): JsonResponse
    {
        try {
            $livrosPorAutor = $this->relatorioService->getRelatorioLivrosPorAutorFormatado();

            // Aplicar filtro se fornecido
            $autorNome = $request->get('autor_nome');
            if ($autorNome) {
                $livrosPorAutor = $livrosPorAutor->filter(function ($autorData) use ($autorNome) {
                    return stripos($autorData['autor'], $autorNome) !== false;
                });
            }

            return response()->json([
                'success' => true,
                'data' => $livrosPorAutor->values(),
                'meta' => $this->relatorioService->getEstatisticasRelatorio(),
                'message' => 'Relatório de livros por autor recuperado com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar relatório: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gera PDF do relatório de livros por autor
     */
    public function livrosPorAutorPDF(Request $request)
    {
        try {
            $pdfContent = $this->relatorioService->gerarRelatorioPDF();

            $filename = 'relatorio-livros-por-autor-' . now()->format('Y-m-d-H-i-s') . '.pdf';

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
