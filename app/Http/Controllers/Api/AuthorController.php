<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Author\StoreAuthorRequest;
use App\Http\Resources\AuthorCollection;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AuthorController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $authors = Author::with('books')->withCount('books')->paginate(10);

        return $this->successResponse(new AuthorCollection($authors), 'Lista de autores', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request): JsonResponse
    {
        try {
            $author = Author::create($request->validated());

            return $this->successResponse(new AuthorResource($author), 'Autor criado com sucesso', 201);
        } catch (Exception $e) {
            Log::error('Erro ao criar autor: ' . $e->getMessage());
            return $this->errorResponse('Erro ao criar autor', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $author = Author::with('books')->withCount('books')->find($id);
        if (!$author) {
            return $this->errorResponse('Autor não encontrado.', 404);
        }

        return $this->successResponse(new AuthorResource($author), 'Autor encontrado com sucesso', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreAuthorRequest $request, string $id): JsonResponse
    {
        $author = Author::find($id);
        if (!$author) {
            return $this->errorResponse('Autor não encontrado.', 404);
        }

        $author->update($request->validated());

        return $this->successResponse(new AuthorResource($author), 'Autor atualizado com sucesso', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $author = Author::find($id);
        if (!$author) {
            return $this->errorResponse('Autor não encontrado.', 404);
        }

        $author->delete();

        return $this->successResponse(null, 'Autor deletado com sucesso');
    }
}
