<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assunto extends Model
{
    use HasFactory;

    protected $table = 'assuntos';
    protected $primaryKey = 'codAs';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'codAs',
        'Descricao'
    ];

    /**
     * Relacionamento many-to-many com Livro
     */
    public function livros()
    {
        return $this->belongsToMany(
            Livro::class,
            'livro_assunto',
            'Assunto_codAs',
            'Livro_Codl',
            'codAs',
            'Codl'
        );
    }
}
