<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    use HasFactory;

    protected $table = 'autores';
    protected $primaryKey = 'CodAu';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'CodAu',
        'Nome'
    ];

    /**
     * Relacionamento many-to-many com Livro
     */
    public function livros()
    {
        return $this->belongsToMany(
            Livro::class,
            'livro_autor',
            'Autor_CodAu',
            'Livro_Codl',
            'CodAu',
            'Codl'
        );
    }
}
