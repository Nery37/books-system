<?php

namespace App\Services;

use App\Repositories\LivroRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LivroService
{
    public function __construct(
        private LivroRepositoryInterface $livroRepository
    ) {}

    public function getAll(array $filters = [])
    {
       return $this->livroRepository->with(['autores', 'assuntos'])->paginate();
    }

    public function getById(int $id)
    {
        try {
            return $this->livroRepository->with(['autores', 'assuntos'])->find($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Livro não encontrado.');
        }
    }

    public function create(array $data)
    {
        return $this->livroRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->livroRepository->update($data, $id);
    }

    public function delete(int $id)
    {
        return $this->livroRepository->delete($id);
    }
}
