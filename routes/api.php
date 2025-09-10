<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\BorrowingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('authors', AuthorController::class);
Route::apiResource('books', BookController::class);
Route::apiResource('members', MemberController::class);
Route::apiResource('borrowings', BorrowingController::class)->only(['index', 'store', 'show']);

Route::post('borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook']);
Route::get('borrowings/overdue/list', [BorrowingController::class, 'overdue']);

Route::get('statistics', function () {
    $totalBooks = \App\Models\Book::count();
    $totalAuthors = \App\Models\Author::count();
    $totalMembers = \App\Models\Member::count();
    $activeBorrowings = \App\Models\Borrowing::where('status', 'borrowed')->count();
    $overdueBorrowings = \App\Models\Borrowing::where('status', 'overdue')->count();

    return response()->json([
        'total_books' => $totalBooks,
        'total_authors' => $totalAuthors,
        'total_members' => $totalMembers,
        'books_borrowed' => $activeBorrowings,
        'overdue_borrowings' => $overdueBorrowings,
    ]);
});
