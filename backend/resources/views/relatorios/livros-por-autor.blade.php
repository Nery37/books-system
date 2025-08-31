<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Livros por Autor</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #2c3e50; }
        .info { margin-bottom: 20px; background-color: #f8f9fa; padding: 10px; border-radius: 5px; }
        .info p { margin: 5px 0; }
        .autor-section { margin-bottom: 25px; page-break-inside: avoid; }
        .autor-header { background-color: #3498db; color: white; padding: 8px; font-weight: bold; font-size: 14px; margin-bottom: 10px; }
        .livros-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .livros-table th { background-color: #ecf0f1; color: #2c3e50; padding: 8px; text-align: left; border: 1px solid #bdc3c7; font-weight: bold; }
        .livros-table td { padding: 6px 8px; border: 1px solid #bdc3c7; vertical-align: top; }
        .livros-table tr:nth-child(even) { background-color: #f8f9fa; }
        .valor { text-align: right; font-weight: bold; color: #27ae60; }
        .assuntos { font-style: italic; color: #7f8c8d; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #7f8c8d; border-top: 1px solid #bdc3c7; padding-top: 10px; }
        .resumo { background-color: #e8f5e8; padding: 10px; border-radius: 5px; margin-top: 20px; }
        .resumo h3 { margin-top: 0; color: #27ae60; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RELATÓRIO DE LIVROS POR AUTOR</h1>
        <h2>Sistema de Gerenciamento de Livros</h2>
    </div>

    <div class="info">
        <p><strong>Data de Geração:</strong> {{ $dataGeracao }}</p>
        @if($filtroAutor)
            <p><strong>Filtro por Autor:</strong> {{ $filtroAutor }}</p>
        @endif
        <p><strong>Total de Autores:</strong> {{ $totalAutores }}</p>
        <p><strong>Total de Livros:</strong> {{ $totalLivros }}</p>
        <p><strong>Valor Total do Acervo:</strong> R$ {{ number_format($valorTotal, 2, ",", ".") }}</p>
    </div>

    @foreach($livrosPorAutor as $autorNome => $livros)
        <div class="autor-section">
            <div class="autor-header">
                {{ $autorNome }} ({{ $livros->count() }} {{ $livros->count() == 1 ? "livro" : "livros" }})
            </div>
            
            <table class="livros-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Editora</th>
                        <th>Edição</th>
                        <th>Ano</th>
                        <th>Valor</th>
                        <th>Assuntos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($livros as $livro)
                        <tr>
                            <td>{{ $livro->livro_titulo }}</td>
                            <td>{{ $livro->livro_editora }}</td>
                            <td>{{ $livro->livro_edicao }}ª</td>
                            <td>{{ $livro->livro_ano }}</td>
                            <td class="valor">R$ {{ number_format($livro->livro_valor, 2, ",", ".") }}</td>
                            <td class="assuntos">{{ $livro->assuntos ?: "Não informado" }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="resumo">
                <h3>Resumo do Autor</h3>
                <p><strong>Total de Livros:</strong> {{ $livros->count() }}</p>
                <p><strong>Valor Total:</strong> R$ {{ number_format($livros->sum("livro_valor"), 2, ",", ".") }}</p>
                <p><strong>Valor Médio:</strong> R$ {{ number_format($livros->avg("livro_valor"), 2, ",", ".") }}</p>
            </div>
        </div>
    @endforeach

    @if($livrosPorAutor->isEmpty())
        <div style="text-align: center; margin-top: 50px; color: #7f8c8d;">
            <p>Nenhum livro encontrado com os filtros aplicados.</p>
        </div>
    @endif

    <div class="footer">
        <p>Relatório gerado automaticamente pelo Sistema de Gerenciamento de Livros</p>
        <p>Página gerada em {{ $dataGeracao }}</p>
    </div>
</body>
</html>