<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\LivroService;
use App\Http\Requests\StoreLivroRequest;
use App\Http\Requests\UpdateLivroRe;
use App\Http\Requests\UpdateLivroRequest;
use App\Transformers\LivroTransformer;
use AppTransformers\LivroTransfquests\ormer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class LivroController extends Controller
{
    protected $fractal;
    protected $livroTransformer;

    public function __construct(
        private LivroService $livroService,
        Manager $fractal,
        LivroTransformer $livroTransformer
    ) {
        $this->fractal = $fractal;
        $this->livroTransformer = $livroTransformer;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'orderBy', 'sortedBy']);
            $include = $request->get('include', '');
            
            if ($include) {
                $this->fractal->parseIncludes($include);
            }

            $livros = $this->livroService->getAll($filters);

            $resource = new Collection($livros->items(), $this->livroTransformer);
            $resource->setPaginator(new IlluminatePaginatorAdapter($livros));

            $data = $this->fractal->createData($resource)->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Livros listados com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar livros.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreLivroRequest $request): JsonResponse
    {
        try {
            $livro = $this->livroService->create($request->validated());

            $resource = new Item($livro, $this->livroTransformer);
            $data = $this->fractal->createData($resource)->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Livro criado com sucesso.'
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
                'message' => 'Erro ao criar livro.',
                'error' => $e->getMessage()
        ],500);
}
   }

    public function               show(Request $request, int $id): JsonResponse
    {
        try {
            $include = $request->get('include', '');
            
            if ($include) {
                $this->fractal->parseIncludes($include);
            }

            $livro = $this->livroService->getById($id);

            $resource = new Item($livro, $this->livroTransformer);
            $data = $this->fractal->createData($resource)->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Livro encontrado com sucesso.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar livro.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateLivroRequest $request, int $id): JsonResponse
    {
        try {
            $livro = $this->livroService->update($id, $request->validated());

            $resource = new Item($livro, $this->livroTransformer);
            $data = $this->fractal->createData($resource)->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Livro atualizado com sucesso.'
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
                'message' => 'Erro ao atualizar livro.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->livroService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Livro excluído com sucesso.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir este livro.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir livro.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
