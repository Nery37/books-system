<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface AutorRepositoryInterface extends RepositoryInterface
{
    public function findByNome(string $nome);
}
