<?php

namespace App\Services;

use App\Repositories\AssuntoRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class AssuntoService
{
    public function __construct(
        private AssuntoRepositoryInterface $assuntoRepository
    ) {}

    public function getAll(array $filters = [])
    {
        $orderBy = $filters['orderBy'] ?? 'codAs';
        $sortedBy = $filters['sortedBy'] ?? 'asc';
        
        return $this->assuntoRepository->with(['livros'])->orderBy($orderBy, $sortedBy)->paginate();
    }

    public function getById(int $id)
    {
        try {
            return $this->assuntoRepository->with(['livros'])->find($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Assunto não encontrado.');
       }
    }

    public function create(array $data)
    {
        return $this->assuntoRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->assuntoRepository->update($data, $id);
    }

    public function delete(int $id)
    {
        return $this->assuntoRepository->delete($id);
    }
}
