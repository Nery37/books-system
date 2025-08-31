<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livros', function (Blueprint $table) {
            $table->increments('Codl');
            $table->string('Titulo', 40);
            $table->string('Editora', 40);
            $table->integer('Edicao');
            $table->string('AnoPublicacao', 4);
            $table->decimal('Valor', 10, 2);
            $table->timestamps();

            $table->index('Titulo');
            $table->index('Editora');
            $table->index('AnoPublicacao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};
