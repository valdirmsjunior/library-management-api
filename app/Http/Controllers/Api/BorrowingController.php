<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Borrowing\StoreBorrowingRequest;
use App\Http\Requests\Borrowing\UpdateBorrowingRequest;
use App\Http\Resources\BorrowingCollection;
use App\Http\Resources\BorrowingResource;
use App\Models\Book;
use App\Models\Borrowing;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BorrowingController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Borrowing::with(['book', 'member']);

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('member_id')) {
            $query->where('member_id', $request->input('member_id'));
        }

        $borrowings = $query->latest()->paginate(15);

        return $this->successResponse(new BorrowingCollection($borrowings), 'Empréstimos listados com sucesso', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowingRequest $request): JsonResponse
    {
        try {
            $book = Book::findOrFail($request->input('book_id'));
            if (!$book->isAvailable()) {
                return $this->errorResponse('O livro não está disponível para empréstimo', 422);
            }

            $borrowing = Borrowing::create($request->validated());
            //atualiza a disponibilidade do livro
            $book->borrow();

            $borrowing->load('book', 'member');

            return $this->successResponse(new BorrowingResource($borrowing), 'Empréstimo registrado com sucesso', 201);
        } catch (Exception $e) {
            Log::error('Erro ao registrar Empréstimo: ' . $e->getMessage());
            return $this->errorResponse('Erro ao registrar Empréstimo', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $borrowing = Borrowing::with(['book', 'member'])->find($id);
            if (!$borrowing) {
                return $this->errorResponse('Empréstimo não encontrado', 404);
            }

            return $this->successResponse(new BorrowingResource($borrowing), 'Empréstimo recuperado com sucesso', 200);
        } catch (Exception $e) {
            Log::error('Erro ao recuperar Empréstimo: ' . $e->getMessage());
            return $this->errorResponse('Erro ao recuperar Empréstimo', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function returnBook(string $id): JsonResponse
    {
        try {
            $borrowing = Borrowing::with(['book', 'member'])->find($id);
            if (!$borrowing) {
                return $this->errorResponse('Empréstimo não encontrado', 404);
            }

            if ($borrowing->status === 'returned') {
                return $this->errorResponse('O livro já foi devolvido', 422);
            }

            $borrowing->update([
                'status' => 'returned',
                'returned_date' => now(),
            ]);
            //atualiza a disponibilidade do livro
            $borrowing->book->returnBook();

            return $this->successResponse(new BorrowingResource($borrowing), 'Livro devolvido com sucesso', 200);
        } catch (Exception $e) {
            Log::error('Erro ao devolver o livro: ' . $e->getMessage());
            return $this->errorResponse('Erro ao devolver o livro', 500);
        }
    }

    public function overdue(): JsonResponse
    {
        try {
            Borrowing::where('status', 'borrowed')
                ->where('due_date', '<', now())
                ->update(['status' => 'overdue']);

            $overdueBorrowings = Borrowing::with(['book', 'member'])
                ->where('status', 'overdue')
                ->where('due_date', '<', now())
                ->get();

            return $this->successResponse(new BorrowingCollection($overdueBorrowings), $overdueBorrowings->isNotEmpty() ? 'Empréstimos atrasados listados com sucesso' : 'Nenhum empréstimo atrasado encontrado', 200);
        } catch (Exception $e) {
            Log::error('Erro ao recuperar Empréstimos atrasados: ' . $e->getMessage());
            return $this->errorResponse('Erro ao recuperar Empréstimos atrasados', 500);
        }
    }
}
