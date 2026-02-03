<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    protected BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Search books by query
     */
    public function search(): JsonResponse
    {
        $query = request('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $books = $this->bookService->searchBooks($query);
        
        return response()->json($books->map(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'category' => $book->category,
                'quantity' => $book->quantity,
                'publisher' => $book->publisher,
                'year' => $book->year,
            ];
        }));
    }

    /**
     * Get books by category
     */
    public function byCategory(string $category): JsonResponse
    {
        $books = $this->bookService->getBooksByCategory($category);
        
        return response()->json($books->map(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'category' => $book->category,
                'quantity' => $book->quantity,
                'publisher' => $book->publisher,
                'year' => $book->year,
            ];
        }));
    }

    /**
     * Get popular books
     */
    public function popular(): JsonResponse
    {
        $books = $this->bookService->getPopularBooks();
        
        return response()->json($books->map(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'category' => $book->category,
                'quantity' => $book->quantity,
                'publisher' => $book->publisher,
                'year' => $book->year,
            ];
        }));
    }

    /**
     * Get recent books
     */
    public function recent(): JsonResponse
    {
        $books = $this->bookService->getRecentBooks();
        
        return response()->json($books->map(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'category' => $book->category,
                'quantity' => $book->quantity,
                'publisher' => $book->publisher,
                'year' => $book->year,
            ];
        }));
    }

    /**
     * Get dashboard statistics
     */
    public function stats(): JsonResponse
    {
        $stats = $this->bookService->getDashboardStats();
        return response()->json($stats);
    }
}

