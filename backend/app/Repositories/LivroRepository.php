<?php

namespace App\Repositories;

use App\Models\Livro;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class LivroRepository extends BaseRepository implements LivroRepositoryInterface
{
    protected $fieldSearchable = [
        'Titulo' => 'like',
        'Editora' => 'like',
        'AnoPublicacao' => '=',
        'autores.Nome' => 'like',
        'assuntos.Descricao' => 'like'
    ];

    public function model()
    {
        return Livro::class;
    }

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function findByTitulo(string $titulo)
    {
        return $this->findWhere(['Titulo' => $titulo]);
    }

    public function findByAutor(string $autor)
    {
        return $this->whereHas('autores', function($query) use ($autor) {
            $query->where('Nome', 'like', "%{$autor}%");
        });
    }

    public function findByAssunto(string $assunto)
    {
        return $this->whereHas('assuntos', function($query) use ($assunto) {
            $query->where('Descricao', 'like', "%{$assunto}%");
        });
    }

    public function searchWithFilters(array $filters)
    {
        $query = $this->with(['autores', 'assuntos']);

        if (isset($filters['titulo'])) {
            $query = $query->where('Titulo', 'like', "%{$filters['titulo']}%");
        }

        if (isset($filters['autor'])) {
            $query = $query->whereHas('autores', function($q) use ($filters) {
                $q->where('Nome', 'like', "%{$filters['autor']}%");
            });
        }

        if (isset($filters['assunto'])) {
            $query = $query->whereHas('assuntos', function($q) use ($filters) {
                $q->where('Descricao', 'like', "%{$filters['assunto']}%");
            });
        }

        return $query;
    }

    public function create(array $attributes)
    {
        // Separar os dados do livro dos relacionamentos
        $autores = $attributes['autores'] ?? [];
        $assuntos = $attributes['assuntos'] ?? [];
        
        // Remover autores e assuntos dos atributos principais
        unset($attributes['autores'], $attributes['assuntos']);
        
        // Criar o livro
        $livro = parent::create($attributes);
        
        // Sincronizar relacionamentos
        if (!empty($autores)) {
            $livro->autores()->sync($autores);
        }
        
        if (!empty($assuntos)) {
            $livro->assuntos()->sync($assuntos);
        }
        
        // Recarregar o livro com os relacionamentos
        return $livro->load(['autores', 'assuntos']);
    }

    public function update(array $attributes, $id)
    {
        // Separar os dados do livro dos relacionamentos
        $autores = $attributes['autores'] ?? [];
        $assuntos = $attributes['assuntos'] ?? [];
        
        // Remover autores e assuntos dos atributos principais
        unset($attributes['autores'], $attributes['assuntos']);
        
        // Atualizar o livro
        $livro = parent::update($attributes, $id);
        
        // Buscar o livro atualizado
        $livroModel = $this->find($id);
        
        // Sincronizar relacionamentos
        if (!empty($autores)) {
            $livroModel->autores()->sync($autores);
        }
        
        if (!empty($assuntos)) {
            $livroModel->assuntos()->sync($assuntos);
        }
        
        // Recarregar o livro com os relacionamentos
        return $livroModel->load(['autores', 'assuntos']);
    }
}
