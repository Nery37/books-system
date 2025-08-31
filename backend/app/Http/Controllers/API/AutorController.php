<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AutorService;
use App\Http\Requests\StoreAutorRequest;
use App\Http\Requests\UpdateAutorRequest;
use App\Transformers\AutorTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AutorController extends Controller
{
    protected $fractal;
    protected $autorTransformer;

    public function __construct(
        private AutorService $autorService,
        Manager $fractal,
        AutorTransformer $autorTransformer
    ) {
        $this->fractal = $fractal;
        $this->autorTransformer = $autorTransformer;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'orderBy', 'sortedBy']); 
           $autores = $this->autorService->getAll($filters);

            $resource = new Collection($autores->items(), $this->autorTransformer);
            $resource->setPaginator(new IlluminatePaginatorAdapter($autores));

            $data = $this->fractal->createData($resource)->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Autores listados om csucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar autores.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAutorRequest $request): JsonResponse
    {
        try {
            $autor = $this->autorService->create($request->validated());

            $resource = new Item($autor, $this->autorTransformer);
            $data = $this->fractal->createData($resource)->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Autor criado com sucesso.'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar autor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $autor = $this->autorService->getById($id);

            $resource = new Item($autor, $this->autorTransformer);
            $data = $this->fractal->createData($resource)->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Autor encontrado com sucesso.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar autor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAutorRequest $request, int $id): JsonResponse
    {
        try {
            $autor = $this->autorService->update($id, $request->validated());

            $resource = new Item($autor, $this->autorTransformer);
            $data = $this->fractal->createData($resource)->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Autor atualizado com sucesso.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar autor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->autorService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Autor excluído com sucesso.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir este autor.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir autor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
