<?php

namespace App\Transformers;

use App\Models\Autor;
use League\Fractal\TransformerAbstract;

class AutorTransformer extends TransformerAbstract
{
    public function transform(Autor $autor)
    {
        return [
            'id' => $autor->CodAu,
            'nome' => $autor->Nome,
            'created_at' => $autor->created_at,
            'updated_at' => $autor->updated_at,
        ];
    }
}
