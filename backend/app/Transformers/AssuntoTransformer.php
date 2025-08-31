<?php

namespace App\Transformers;

use App\Models\Assunto;
use League\Fractal\TransformerAbstract;

class AssuntoTransformer extends TransformerAbstract
{
    public function transform(Assunto $assunto)
    {
        return [
            'id' => $assunto->codAs,
            'descricao' => $assunto->Descricao,
            'created_at' => $assunto->created_at,
            'updated_at' => $assunto->updated_at,
        ];
    }
}
