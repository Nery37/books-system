<?php

namespace App\Repositories;

use App\Models\Assunto;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class AssuntoRepository extends BaseRepository implements AssuntoRepositoryInterface
{
    protected $fieldSearchable = [
        'Descricao' => 'like'
    ];

    public function model()
    {
        return Assunto::class;
    }

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function findByDescricao(string $descricao)
    {
        return $this->findWhere(['Descricao' => $descricao]);
    }
}
