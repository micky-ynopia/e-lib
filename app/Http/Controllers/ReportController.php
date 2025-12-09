<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Book;
use App\Models\User;
use App\Models\BookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show borrows report
     */
    public function borrows(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $query = Borrow::with(['user', 'book']);

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('borrowed_at', '>=', $request->get('from_date'));
        }
        if ($request->has('to_date')) {
            $query->whereDate('borrowed_at', '<=', $request->get('to_date'));
        }

        // Filter by status
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        $borrows = $query->orderByDesc('borrowed_at')->paginate(50);

        return view('reports.borrows', compact('borrows'));
    }

    /**
     * Show books report
     */
    public function books(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $query = Book::with(['author', 'category']);

        // Filter by category
        if ($request->has('category') && $request->get('category') !== '') {
            $query->where('category_id', $request->get('category'));
        }

        // Filter by status
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        // Filter by availability
        if ($request->has('availability')) {
            if ($request->get('availability') === 'available') {
                $query->where('available_copies', '>', 0);
            } elseif ($request->get('availability') === 'unavailable') {
                $query->where('available_copies', 0);
            }
        }

        $books = $query->orderBy('title')->paginate(50);

        return view('reports.books', compact('books'));
    }

    /**
     * Show overdue report
     */
    public function overdue(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $query = Borrow::with(['user', 'book'])
            ->where('status', 'overdue')
            ->whereNull('returned_at');

        // Filter by days overdue
        if ($request->has('days_min')) {
            $daysMin = $request->get('days_min');
            $query->where('due_at', '<=', now()->subDays($daysMin));
        }

        $overdueBorrows = $query->orderBy('due_at')->paginate(50);

        return view('reports.overdue', compact('overdueBorrows'));
    }

    /**
     * Show popular books report
     */
    public function popularBooks(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $days = $request->get('days', 30);
        $limit = $request->get('limit', 20);

        $books = Book::withCount('borrows')
            ->with(['author', 'category'])
            ->orderByDesc('borrows_count')
            ->limit($limit)
            ->get();

        // Get borrows count for the specified period
        $recentBorrowsCount = Borrow::where('borrowed_at', '>=', now()->subDays($days))
            ->selectRaw('book_id, count(*) as count')
            ->groupBy('book_id')
            ->pluck('count', 'book_id');

        return view('reports.popular-books', compact('books', 'recentBorrowsCount', 'days'));
    }

    /**
     * Show statistics summary
     */
    public function statistics()
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $stats = [
            'total_books' => Book::count(),
            'total_physical_books' => Book::whereIn('book_type', ['physical', 'both'])->count(),
            'total_digital_books' => Book::whereIn('book_type', ['digital', 'both'])->count(),
            'total_authors' => \App\Models\Author::count(),
            'total_categories' => \App\Models\Category::count(),
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_staff' => User::where('role', 'staff')->count(),
            'total_librarians' => User::where('role', 'librarian')->count(),
            'pending_approvals' => User::where('is_approved', false)->count(),
            'total_borrows' => Borrow::count(),
            'active_borrows' => Borrow::where('status', 'borrowed')->count(),
            'overdue_borrows' => Borrow::where('status', 'overdue')->count(),
            'returned_borrows' => Borrow::where('status', 'returned')->count(),
            'total_book_requests' => BookRequest::count(),
            'pending_requests' => BookRequest::where('status', 'pending')->count(),
            'approved_requests' => BookRequest::where('status', 'approved')->count(),
            'fulfilled_requests' => BookRequest::where('status', 'fulfilled')->count(),
            'total_theses' => \App\Models\Thesis::count(),
            'approved_theses' => \App\Models\Thesis::where('status', 'approved')->count(),
            'total_announcements' => \App\Models\Announcement::count(),
            'published_announcements' => \App\Models\Announcement::where('is_published', true)->count(),
        ];

        // Monthly statistics
        $monthlyBorrows = Borrow::whereYear('borrowed_at', now()->year)
            ->whereMonth('borrowed_at', now()->month)
            ->count();

        $stats['monthly_borrows'] = $monthlyBorrows;

        return view('reports.statistics', compact('stats'));
    }

    /**
     * Export borrows report as CSV
     */
    public function exportBorrows(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $query = Borrow::with(['user', 'book']);

        // Apply same filters as index
        if ($request->has('from_date')) {
            $query->whereDate('borrowed_at', '>=', $request->get('from_date'));
        }
        if ($request->has('to_date')) {
            $query->whereDate('borrowed_at', '<=', $request->get('to_date'));
        }
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        $borrows = $query->orderByDesc('borrowed_at')->get();

        $filename = 'borrows_report_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($borrows) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['ID', 'User', 'Book', 'Borrowed At', 'Due At', 'Returned At', 'Status', 'Fine Amount', 'Fine Paid At']);
            
            // Data
            foreach ($borrows as $borrow) {
                fputcsv($file, [
                    $borrow->id,
                    $borrow->user->name,
                    $borrow->book->title,
                    $borrow->borrowed_at?->format('Y-m-d'),
                    $borrow->due_at?->format('Y-m-d'),
                    $borrow->returned_at?->format('Y-m-d'),
                    $borrow->status,
                    number_format($borrow->fine_amount ?? 0, 2),
                    $borrow->fine_paid_at?->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export books report as CSV
     */
    public function exportBooks(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $query = Book::with(['author', 'category']);

        // Apply same filters as index
        if ($request->has('category') && $request->get('category') !== '') {
            $query->where('category_id', $request->get('category'));
        }
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }
        if ($request->has('availability')) {
            if ($request->get('availability') === 'available') {
                $query->where('available_copies', '>', 0);
            } elseif ($request->get('availability') === 'unavailable') {
                $query->where('available_copies', 0);
            }
        }

        $books = $query->orderBy('title')->get();

        $filename = 'books_report_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($books) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['ID', 'Title', 'Author', 'Category', 'ISBN', 'Type', 'Total Copies', 'Available Copies', 'Status']);
            
            // Data
            foreach ($books as $book) {
                fputcsv($file, [
                    $book->id,
                    $book->title,
                    $book->author->name ?? 'N/A',
                    $book->category->name ?? 'N/A',
                    $book->isbn,
                    $book->book_type,
                    $book->total_copies,
                    $book->available_copies,
                    $book->status,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export overdue report as CSV
     */
    public function exportOverdue(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $query = Borrow::with(['user', 'book'])
            ->where('status', 'overdue')
            ->whereNull('returned_at');

        if ($request->has('days_min')) {
            $daysMin = $request->get('days_min');
            $query->where('due_at', '<=', now()->subDays($daysMin));
        }

        $overdueBorrows = $query->orderBy('due_at')->get();

        $filename = 'overdue_report_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($overdueBorrows) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['ID', 'User', 'Book', 'Borrowed At', 'Due At', 'Days Overdue', 'Fine Amount', 'Fine Paid']);
            
            // Data
            foreach ($overdueBorrows as $borrow) {
                $daysOverdue = $borrow->due_at ? max(0, now()->diffInDays($borrow->due_at)) : 0;
                fputcsv($file, [
                    $borrow->id,
                    $borrow->user->name,
                    $borrow->book->title,
                    $borrow->borrowed_at?->format('Y-m-d'),
                    $borrow->due_at?->format('Y-m-d'),
                    $daysOverdue,
                    number_format($borrow->fine_amount ?? 0, 2),
                    $borrow->fine_paid_at ? 'Yes' : 'No',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

