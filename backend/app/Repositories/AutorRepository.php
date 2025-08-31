<?php

namespace App\Repositories;

use App\Models\Autor;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class AutorRepository extends BaseRepository implements AutorRepositoryInterface
{
    protected $fieldSearchable = [
        'Nome' => 'like'
    ];

    public function model()
    {
        return Autor::class;
    }

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function findByNome(string $nome)
    {
        return $this->findWhere(['Nome' => $nome]);
    }
}
