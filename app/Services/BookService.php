<?php

namespace App\Services;

use App\Models\InventoriBuku;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BookService
{
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return [
            'total_books' => InventoriBuku::sum('quantity'),
            'total_titles' => InventoriBuku::count(),
            'active_loans' => Peminjaman::where('status', 'borrowed')
                ->where('confirmation_status', 'approved')
                ->count(),
            'pending_returns' => Pengembalian::where('confirmation_status', 'pending')->count(),
            'pending_confirmations' => Peminjaman::where('confirmation_status', 'pending')->count(),
            'total_members' => User::where('status', 'active')->count(),
            'overdue_loans' => Peminjaman::where('status', 'borrowed')
                ->where('due_date', '<', now())
                ->count(),
        ];
    }

    /**
     * Get popular books based on borrow count
     */
    public function getPopularBooks(int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        return InventoriBuku::withCount('peminjaman')
            ->orderByDesc('peminjaman_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent additions
     */
    public function getRecentBooks(int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        return InventoriBuku::latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get available books
     */
    public function getAvailableBooks(int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        return InventoriBuku::where('quantity', '>', 0)
            ->orderByDesc('quantity')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all categories
     */
    public function getCategories(): array
    {
        return InventoriBuku::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->toArray();
    }

    /**
     * Get books by category
     */
    public function getBooksByCategory(string $category, int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        return InventoriBuku::where('category', $category)
            ->where('quantity', '>', 0)
            ->limit($limit)
            ->get();
    }

    /**
     * Search books
     */
    public function searchBooks(string $query, int $limit = 12): \Illuminate\Database\Eloquent\Collection
    {
        return InventoriBuku::where('title', 'like', "%{$query}%")
            ->orWhere('author', 'like', "%{$query}%")
            ->orWhere('isbn', 'like', "%{$query}%")
            ->orWhere('category', 'like', "%{$query}%")
            ->where('quantity', '>', 0)
            ->limit($limit)
            ->get();
    }

    /**
     * Get monthly borrowing statistics for chart
     */
    public function getMonthlyStats(int $year = null): array
    {
        $year = $year ?? now()->year;
        
        $stats = Peminjaman::select(
            DB::raw('MONTH(borrow_date) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('borrow_date', $year)
        ->where('confirmation_status', 'approved')
        ->groupBy(DB::raw('MONTH(borrow_date)'))
        ->orderBy('month')
        ->get();

        $data = array_fill(0, 12, 0);
        foreach ($stats as $stat) {
            $data[$stat->month - 1] = $stat->count;
        }

        return [
            'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'data' => $data,
        ];
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Peminjaman::with(['user', 'inventoriBuku'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get pending confirmations
     */
    public function getPendingConfirmations(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Peminjaman::with(['user', 'inventoriBuku'])
            ->where('confirmation_status', 'pending')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get low stock books
     */
    public function getLowStockBooks(int $threshold = 3): \Illuminate\Database\Eloquent\Collection
    {
        return InventoriBuku::where('quantity', '<=', $threshold)
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'asc')
            ->get();
    }

    /**
     * Get out of stock books
     */
    public function getOutOfStockBooks(): \Illuminate\Database\Eloquent\Collection
    {
        return InventoriBuku::where('quantity', 0)
            ->orderBy('title')
            ->get();
    }
}

