<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface LivroRepositoryInterface extends RepositoryInterface
{
    public function findByTitulo(string $titulo);
    public function findByAutor(string $autor);
    public function findByAssunto(string $assunto);
    public function searchWithFilters(array $filters);
}
