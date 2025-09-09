<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Book::with('author');

        // Apply search filters, colocar esse trecho no service
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%")
                ->orWhereHas('author', function ($authorQuery) use ($search) {
                    $authorQuery->where('name', 'like', "%{$search}%");
                });
            });
        }
        //mover posteriormente para service
        if ($request->has('genre')) {
            $query->where('genre', $request->input('genre'));
        }

        $books = $query->paginate(10);

        return $this->successResponse(new BookCollection($books), 'Livros listados com sucesso', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        try {
            $book = Book::create($request->validated());
            $book->load('author');

            return $this->successResponse(new BookResource($book), 'Livro adicionado com sucesso', 201);
        } catch (Exception $e) {
            Log::error('Erro ao adicionar livro: ' . $e->getMessage());
            return $this->errorResponse('Erro ao adicionar livro', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $book = Book::with('author')->find($id);
            if (!$book) {
                return $this->errorResponse('Livro não encontrado.', 404);
            }

            return $this->successResponse(new BookResource($book), 'Livro encontrado com sucesso', 200);
        } catch (Exception $e) {
            Log::error('Erro ao buscar livro: ' . $e->getMessage());
            return $this->errorResponse('Erro ao buscar livro', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, string $id): JsonResponse
    {
        try {
            $book = Book::find($id);
            if (!$book) {
                return $this->errorResponse('Livro não encontrado.', 404);
            }

            $book->update($request->validated());
            $book->load('author');

            return $this->successResponse(new BookResource($book), 'Livro atualizado com sucesso', 200);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar livro: ' . $e->getMessage());
            return $this->errorResponse('Erro ao atualizar livro', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $book = Book::find($id);
            if (!$book) {
                return $this->errorResponse('Livro não encontrado.', 404);
            }

            $book->delete();

            return $this->successResponse(null, 'Livro deletado com sucesso');
        } catch (Exception $e) {
            Log::error('Erro ao deletar livro: ' . $e->getMessage());
            return $this->errorResponse('Erro ao deletar livro', 500);
        }
    }
}
