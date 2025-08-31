<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livro extends Model
{
    use HasFactory;

    protected $table = 'livros';
    protected $primaryKey = 'Codl';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'Codl',
        'Titulo',
        'Editora',
        'Edicao',
        'AnoPublicacao',
        'Valor'
    ];

    protected $casts = [
        'Valor' => 'decimal:2'
    ];

    /**
     * Relacionamento many-to-many com Autor
     */
    public function autores()
    {
        return $this->belongsToMany(
            Autor::class,
            'livro_autor',
            'Livro_Codl',
            'Autor_CodAu',
            'Codl',
            'CodAu'
        );
    }

    /**
     * Relacionamento many-to-many com Assunto
     */
    public function assuntos()
    {
        return $this->belongsToMany(
            Assunto::class,
            'livro_assunto',
            'Livro_Codl',
            'Assunto_codAs',
            'Codl',
            'codAs'
        );
    }
}
