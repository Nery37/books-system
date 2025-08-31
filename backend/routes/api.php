<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AutorController;
use App\Http\Controllers\API\AssuntoController;
use App\Http\Controllers\API\LivroController;
use App\Http\Controllers\API\RelatorioController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas da API
Route::prefix('v1')->group(function () {
    // CRUD Autores
    Route::apiResource('autores', AutorController::class);
    
    // CRUD Assuntos
    Route::apiResource('assuntos', AssuntoController::class);
    
    // CRUD Livros
    Route::apiResource('livros', LivroController::class);
    
    // Relatórios
    Route::prefix('relatorios')->group(function () {
        Route::get('livros-por-autor', [RelatorioController::class, 'livrosPorAutor']);
        Route::get('livros-por-autor/pdf', [RelatorioController::class, 'livrosPorAutorPDF']);
        Route::get('estatisticas', [RelatorioController::class, 'estatisticas']);
    });
});
