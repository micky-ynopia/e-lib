<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Create cache key based on filters
        $cacheKey = 'books_' . md5(json_encode($request->all()));
        
        $books = Cache::remember($cacheKey, 3600, function () use ($request) {
            $query = Book::with(['author','category']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('author', function ($authorQuery) use ($search) {
                      $authorQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by category
        if ($request->has('category') && $request->get('category') !== '') {
            $query->where('category_id', $request->get('category'));
        }

        // Filter by book type
        if ($request->has('book_type') && $request->get('book_type') !== '') {
            $query->where('book_type', $request->get('book_type'));
        }

        // Filter by status
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        // Filter by featured
        if ($request->has('featured') && $request->get('featured') === '1') {
            $query->where('is_featured', true);
        }

            return $query->orderBy('title')->paginate(12);
        });

        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403);
        }
        $authors = Author::orderBy('last_name')->get();
        $categories = Category::orderBy('name')->get();
        return view('books.create', compact('authors','categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'nullable|exists:categories,id',
            'isbn' => 'required|string|max:50|unique:books,isbn',
            'published_year' => 'nullable|integer|min:0|max:9999',
            'total_copies' => 'required|integer|min:1',
            'book_type' => 'required|in:physical,digital,both',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
            'is_featured' => 'boolean',
            'book_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file upload for digital books
        if ($request->hasFile('book_file')) {
            $file = $request->file('book_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('books', $filename, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $cover = $request->file('cover_image');
            $coverName = time() . '_' . $cover->getClientOriginalName();
            $coverPath = $cover->storeAs('covers', $coverName, 'public');
            
            $validated['cover_image'] = $coverPath;
        }

        $validated['available_copies'] = $validated['total_copies'];
        $validated['approved_by'] = Auth::id();
        $validated['approved_at'] = now();
        
        Book::create($validated);
        return redirect()->route('books.index')->with('status', 'Book created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load(['author','category','borrows']);
        $book->increment('view_count');
        return view('books.show', compact('book'));
    }

    /**
     * View/Read digital book in browser
     */
    public function read(Book $book)
    {
        if (!$book->isDigital()) {
            abort(404, 'This book is not available for reading.');
        }

        // Check if file exists - try different path formats
        $filePath = $book->file_path;
        $fileExists = false;
        
        if ($filePath) {
            // Try the stored path first
            if (Storage::disk('public')->exists($filePath)) {
                $fileExists = true;
            } 
            // Try with books/ prefix if not already there
            elseif (!str_starts_with($filePath, 'books/') && Storage::disk('public')->exists('books/' . basename($filePath))) {
                $filePath = 'books/' . basename($filePath);
                $book->update(['file_path' => $filePath]);
                $fileExists = true;
            }
        }
        
        if (!$fileExists) {
            return redirect()->route('books.show', $book)
                ->with('error', 'PDF file not available yet. Please check back later or contact the librarian.');
        }

        // Increment view count when opening the book
        $book->increment('view_count');
        
        // Return the PDF viewer page
        return view('books.read', compact('book'));
    }

    /**
     * Download digital book
     */
    public function download(Book $book)
    {
        if (!$book->isDigital()) {
            abort(404, 'This book is not available for download.');
        }

        // Check if file exists - try different path formats
        $filePath = $book->file_path;
        $fileExists = false;
        
        if ($filePath) {
            // Try the stored path first
            if (Storage::disk('public')->exists($filePath)) {
                $fileExists = true;
            } 
            // Try with books/ prefix if not already there
            elseif (!str_starts_with($filePath, 'books/') && Storage::disk('public')->exists('books/' . basename($filePath))) {
                $filePath = 'books/' . basename($filePath);
                $book->update(['file_path' => $filePath]);
                $fileExists = true;
            }
        }
        
        if (!$fileExists) {
            return redirect()->route('books.show', $book)
                ->with('error', 'PDF file not available yet. Please check back later or contact the librarian.');
        }

        $book->increment('download_count');
        return Storage::disk('public')->download($filePath, $book->file_name);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403);
        }
        $authors = Author::orderBy('last_name')->get();
        $categories = Category::orderBy('name')->get();
        return view('books.edit', compact('book','authors','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'nullable|exists:categories,id',
            'isbn' => 'required|string|max:50|unique:books,isbn,' . $book->id,
            'published_year' => 'nullable|integer|min:0|max:9999',
            'total_copies' => 'required|integer|min:1',
            'book_type' => 'required|in:physical,digital,both',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
            'is_featured' => 'boolean',
            'book_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle PDF file upload for digital books
        if ($request->hasFile('book_file')) {
            // Delete old file if exists
            if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
                Storage::disk('public')->delete($book->file_path);
            }
            
            $file = $request->file('book_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('books', $filename, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old cover if exists
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            
            $cover = $request->file('cover_image');
            $coverName = time() . '_' . $cover->getClientOriginalName();
            $coverPath = $cover->storeAs('covers', $coverName, 'public');
            
            $validated['cover_image'] = $coverPath;
        }

        // Adjust available copies if total changed (keep at least borrows outstanding)
        $originalTotal = $book->total_copies;
        $originalAvailable = $book->available_copies;
        $book->fill($validated);
        if ($validated['total_copies'] != $originalTotal) {
            $difference = $validated['total_copies'] - $originalTotal;
            $book->available_copies = max(0, $originalAvailable + $difference);
        }
        $book->save();

        return redirect()->route('books.show', $book)->with('status', 'Book updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403);
        }

        // Delete associated files
        if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
            Storage::disk('public')->delete($book->file_path);
        }
        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();
        return redirect()->route('books.index')->with('status', 'Book deleted.');
    }

    /**
     * Bulk delete books
     */
    public function bulkDelete(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $request->validate([
            'book_ids' => 'required|array',
            'book_ids.*' => 'exists:books,id',
        ]);

        $books = Book::whereIn('id', $request->get('book_ids'))->get();
        $count = 0;

        foreach ($books as $book) {
            // Delete associated files
            if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
                Storage::disk('public')->delete($book->file_path);
            }
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $book->delete();
            $count++;
        }

        return redirect()->back()->with('status', "Successfully deleted {$count} books.");
    }

    /**
     * Bulk approve books
     */
    public function bulkApprove(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $request->validate([
            'book_ids' => 'required|array',
            'book_ids.*' => 'exists:books,id',
        ]);

        $count = Book::whereIn('id', $request->get('book_ids'))
            ->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

        return redirect()->back()->with('status', "Successfully approved {$count} books.");
    }

    /**
     * Bulk reject books
     */
    public function bulkReject(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $request->validate([
            'book_ids' => 'required|array',
            'book_ids.*' => 'exists:books,id',
        ]);

        $count = Book::whereIn('id', $request->get('book_ids'))
            ->update([
                'status' => 'rejected',
                'approved_at' => null,
            ]);

        return redirect()->back()->with('status', "Successfully rejected {$count} books.");
    }
}
