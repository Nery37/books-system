<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AssuntoService;
use App\Http\Requests\StoreAssuntoRequest;
use App\Http\Requests\UpdateAssuntoRequest;
use App\Transformers\AssuntoTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AssuntoController extends Controller
{
    protected $fractal;
    protected $assuntoTransformer;

    public function __construct(
        private AssuntoService $assuntoService,
        Manager $fractal,
        AssuntoTransformer $assuntoTransformer
    ) {
        $this->fractal = $fractal;
        $this->assuntoTransformer = $assuntoTransformer;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'orderBy', 'sortedBy']);
            $assuntos = $this->assuntoService->getAll($filters);

            $resource = new Collection($assuntos->items(), $this->assuntoTransformer);
            $resource->setPaginator(new IlluminatePaginatorAdapter($assuntos));

            $data = $this->fractal->createData($resource)->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message'=>'Assuntoslistadoscomsucesso.'
                 ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar assuntos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssuntoRequest $request): JsonResponse
    {
        try {
            $assunto = $this->assuntoService->create($request->validated());

            $resource = new Item($assunto, $this->assuntoTransformer);
            $data = $this->fractal->createData($resource)->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Assunto criado com sucesso.'
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
                'message' => 'Erro ao criar assunto.',
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
            $assunto = $this->assuntoService->getById($id);

            $resource = new Item($assunto, $this->assuntoTransformer);
            $data = $this->fractal->createData($resource)->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Assunto encontrado com sucesso.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar assunto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssuntoRequest $request, int $id): JsonResponse
    {
        try {
            $assunto = $this->assuntoService->update($id, $request->validated());

            $resource = new Item($assunto, $this->assuntoTransformer);
            $data = $this->fractal->createData($resource)->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Assunto atualizado com sucesso.'
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
                'message' => 'Erro ao atualizar assunto.',
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
            $this->assuntoService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Assunto excluído com sucesso.'
            ]);
        } catch (ModelNotFoundException$e){
     return response()->json([
                         'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success'=>false,
             'message' => 'Não      é possível excluir este assunto.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir assunto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
