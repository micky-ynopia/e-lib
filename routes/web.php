<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookRequestController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ThesisController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReserveController;
use App\Http\Controllers\ReviewController;

Route::get('/', function () {
    $featuredBooks = \App\Models\Book::where('is_featured', true)
        ->where('status', 'approved')
        ->with(['author', 'category'])
        ->latest()
        ->take(6)
        ->get();
    
    $stats = [
        'total_books' => \App\Models\Book::where('status', 'approved')->count(),
        'total_categories' => \App\Models\Category::count(),
        'total_authors' => \App\Models\Author::count(),
        'digital_books' => \App\Models\Book::where('status', 'approved')
            ->whereIn('book_type', ['digital', 'both'])
            ->count(),
    ];
    
    return view('home', compact('featuredBooks', 'stats'));
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard routes
    Route::get('/student/dashboard', [AuthController::class, 'studentDashboard'])->name('student.dashboard');
    Route::get('/staff/dashboard', [AuthController::class, 'staffDashboard'])->name('staff.dashboard');
    Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard'])->name('admin.dashboard');
    
    // Resource routes
    Route::resource('authors', AuthorController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('books', BookController::class);
    Route::resource('borrows', BorrowController::class);
    Route::resource('book-requests', BookRequestController::class);
    Route::resource('announcements', AnnouncementController::class);
    Route::resource('theses', ThesisController::class);
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    
    // Custom user routes
    Route::post('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
    
    // Thesis download route
    Route::get('/theses/{thesis}/download', [ThesisController::class, 'download'])->name('theses.download');
    
    // Book routes
    Route::get('/books/{book}/read', [BookController::class, 'read'])->name('books.read');
    Route::get('/books/{book}/download', [BookController::class, 'download'])->name('books.download');
    
    // Favorite routes
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/books/{book}/favorite', [FavoriteController::class, 'store'])->name('books.favorite');
    Route::delete('/books/{book}/favorite', [FavoriteController::class, 'destroy'])->name('books.unfavorite');
    Route::post('/books/{book}/toggle-favorite', [FavoriteController::class, 'toggle'])->name('books.toggle-favorite');
    
    // Book request download route
    Route::get('/book-requests/{bookRequest}/download', [BookRequestController::class, 'download'])->name('book-requests.download');
    
    // Reserve routes
    Route::resource('reserves', ReserveController::class);
    Route::post('/reserves/{reserve}/fulfill', [ReserveController::class, 'fulfill'])->name('reserves.fulfill');
    Route::get('/books/{book}/queue', [ReserveController::class, 'queue'])->name('books.queue');
    
    // Review routes
    Route::get('/books/{book}/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    
    // Fine payment route
    Route::post('/borrows/{borrow}/pay-fine', [BorrowController::class, 'payFine'])->name('borrows.pay-fine');
    
    // Reports routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/borrows', [ReportController::class, 'borrows'])->name('borrows');
        Route::get('/books', [ReportController::class, 'books'])->name('books');
        Route::get('/overdue', [ReportController::class, 'overdue'])->name('overdue');
        Route::get('/popular-books', [ReportController::class, 'popularBooks'])->name('popular-books');
        Route::get('/statistics', [ReportController::class, 'statistics'])->name('statistics');
        
        // Export routes
        Route::get('/borrows/export', [ReportController::class, 'exportBorrows'])->name('borrows.export');
        Route::get('/books/export', [ReportController::class, 'exportBooks'])->name('books.export');
        Route::get('/overdue/export', [ReportController::class, 'exportOverdue'])->name('overdue.export');
    });
    
    // Bulk operations routes
    Route::post('/users/bulk-approve', [UserController::class, 'bulkApprove'])->name('users.bulk-approve');
    Route::post('/users/bulk-reject', [UserController::class, 'bulkReject'])->name('users.bulk-reject');
    Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    
    Route::post('/books/bulk-approve', [BookController::class, 'bulkApprove'])->name('books.bulk-approve');
    Route::post('/books/bulk-reject', [BookController::class, 'bulkReject'])->name('books.bulk-reject');
    Route::post('/books/bulk-delete', [BookController::class, 'bulkDelete'])->name('books.bulk-delete');
});
