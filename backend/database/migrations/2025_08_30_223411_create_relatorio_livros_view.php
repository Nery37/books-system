<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_relatorio_livros");
        
        DB::statement("
            CREATE VIEW vw_relatorio_livros AS
            SELECT 
                l.Codl as livro_id,
                l.Titulo as livro_titulo,
                l.Editora as livro_editora,
                l.Edicao as livro_edicao,
                l.AnoPublicacao as livro_ano,
                l.Valor as livro_valor,
                a.CodAu as autor_id,
                a.Nome as autor_nome,
                GROUP_CONCAT(DISTINCT ass.Descricao SEPARATOR ', ') as assuntos
            FROM livros l
            INNER JOIN livro_autor la ON l.Codl = la.Livro_Codl
            INNER JOIN autores a ON la.Autor_CodAu = a.CodAu
            LEFT JOIN livro_assunto las ON l.Codl = las.Livro_Codl
            LEFT JOIN assuntos ass ON las.Assunto_codAs = ass.codAs
            GROUP BY l.Codl, l.Titulo, l.Editora, l.Edicao, l.AnoPublicacao, l.Valor, a.CodAu, a.Nome   
            ORDER BY a.Nome, l.Titulo
        ");
    }    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_relatorio_livros");
    }
};
