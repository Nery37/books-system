<?php

namespace App\Services;

use App\Repositories\AutorRepositoryInterface;
use App\Transformers\AutorTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class AutorService
{
    public function __construct(
        private AutorRepositoryInterface $autorRepository
    ) {}

    public function getAll(array $filters = [])
    {
        $orderBy = $filters['orderBy'] ?? 'CodAu';
        $sortedBy = $filters['sortedBy'] ?? 'asc';
        
        return $this->autorRepository->with(['livros'])->orderBy($orderBy, $sortedBy)->paginate();
    }

    public function getById(int $id)
    {
        try {
            return $this->autorRepository->with(['livros'])->find($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Autor não encontrado.');
        }
    }

    public function create(array $data)
    {
        $this->validateUniqueNome($data['Nome'] ?? '');

        return $this->autorRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $autor = $this->getById($id);

        if (isset($data['Nome']) && $data['Nome'] !== $autor->Nome) {
            $this->validateUniqueNome($data['Nome']);
        }

        return $this->autorRepository->update($data, $id);
    }

    public function delete(int $id)
    {
        $autor = $this->getById($id);

        if ($autor->livros()->count()>0){
          throw ValidationException::withMessages([
                'error' => 'Não é possível excluir este autor pois possui livros associados.'
            ]);
        }

        return $this->autorRepository->delete($id);
    }

    private function validateUniqueNome(string $nome)
    {
        $existingAutor = $this->autorRepository->findByNome($nome);

        if ($existingAutor->count() > 0) {
            throw ValidationException::withMessages([
                'Nome' => 'Já existe um autor com este nome.'
            ]);
        }
    }
}
