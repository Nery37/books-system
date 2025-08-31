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
        $query = $this->livroRepository->with(['autores', 'assuntos']);
        
        // Filtro por título
        if (isset($filters['titulo']) && !empty($filters['titulo'])) {
            $query = $query->findByField('titulo', 'like', '%' . $filters['titulo'] . '%');
        }
        
        // Filtro por autor
        if (isset($filters['autor_id']) && !empty($filters['autor_id'])) {
            $query = $query->whereHas('autores', function($q) use ($filters) {
                $q->where('autores.id', $filters['autor_id']);
            });
        }
        
        // Filtro por assunto
        if (isset($filters['assunto_id']) && !empty($filters['assunto_id'])) {
            $query = $query->whereHas('assuntos', function($q) use ($filters) {
                $q->where('assuntos.id', $filters['assunto_id']);
            });
        }
        
        // Busca geral
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query = $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', '%' . $search . '%')
                  ->orWhere('editora', 'like', '%' . $search . '%');
            });
        }
        
        // Ordenação
        $orderBy = $filters['orderBy'] ?? 'titulo';
        $sortedBy = $filters['sortedBy'] ?? 'asc';
        
        return $query->orderBy($orderBy, $sortedBy)->paginate(15);
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
