<?php

namespace App\Transformers;

use App\Models\Livro;
use League\Fractal\TransformerAbstract;

class LivroTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'autores',
        'assuntos'
    ];

    public function transform(Livro $livro)
    {
        return [
            'id' => $livro->Codl,
            'titulo' => $livro->Titulo,
            'editora' => $livro->Editora,
            'edicao' => $livro->Edicao,
            'anoPublicacao' => $livro->AnoPublicacao,
            'valor' => $livro->Valor,
            'valor_formatado' => 'R$ ' . number_format($livro->Valor, 2, ',', '.'),
            'created_at' => $livro->created_at,
            'updated_at' => $livro->updated_at,
        ];
    }

    public function includeAutores(Livro $livro)
    {
        return $this->collection($livro->autores, new AutorTransformer);
    }

    public function includeAssuntos(Livro $livro)
    {
        return $this->collection($livro->assuntos, new AssuntoTransformer);
    }
}
