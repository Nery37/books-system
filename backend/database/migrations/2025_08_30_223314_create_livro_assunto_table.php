<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('livro_assunto', function (Blueprint $table) {
            $table->unsignedInteger('Livro_Codl');
            $table->unsignedInteger('Assunto_codAs');
            $table->timestamps();

            $table->primary(['Livro_Codl', 'Assunto_codAs']);
            $table->foreign('Livro_Codl')->references('Codl')->on('livros')->onDelete('cascade');       
            $table->foreign('Assunto_codAs')->references('codAs')->on('assuntos')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livro_assunto');
    }
};
