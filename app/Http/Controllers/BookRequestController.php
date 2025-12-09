<?php

namespace App\Http\Controllers;

use App\Models\BookRequest;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isStudent()) {
            // Students see only their own requests
            $bookRequests = $user->bookRequests()->with(['book', 'approver', 'fulfiller'])
                ->latest()
                ->paginate(10);
        } else {
            // Staff and librarians see all requests
            $bookRequests = BookRequest::with(['user', 'book', 'approver', 'fulfiller'])
                ->latest()
                ->paginate(10);
        }

        return view('book-requests.index', compact('bookRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            abort(403, 'Only students can request books.');
        }

        if (!$user->is_approved) {
            return redirect()->route('student.dashboard')->with('error', 
                'Your account must be approved before you can request books.');
        }

        $books = Book::where('status', 'approved')
            ->where('available_copies', '>', 0)
            ->with(['author', 'category'])
            ->orderBy('title')
            ->get();

        return view('book-requests.create', compact('books'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            abort(403, 'Only students can request books.');
        }

        if (!$user->is_approved) {
            return redirect()->route('student.dashboard')->with('error', 
                'Your account must be approved before you can request books.');
        }

        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'request_type' => 'required|in:physical,digital',
            'notes' => 'nullable|string|max:500',
        ]);

        $book = Book::findOrFail($validated['book_id']);

        // Check if book is available
        if ($book->status !== 'approved') {
            return back()->withErrors(['book_id' => 'This book is not available for request.']);
        }

        if ($validated['request_type'] === 'physical' && $book->available_copies < 1) {
            return back()->withErrors(['book_id' => 'This book has no available physical copies.']);
        }

        // Check if user already has a pending request for this book
        $existingRequest = $user->bookRequests()
            ->where('book_id', $validated['book_id'])
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRequest) {
            return back()->withErrors(['book_id' => 'You already have a pending or approved request for this book.']);
        }

        $bookRequest = BookRequest::create([
            'user_id' => $user->id,
            'book_id' => $validated['book_id'],
            'request_type' => $validated['request_type'],
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);

        return redirect()->route('book-requests.show', $bookRequest)
            ->with('status', 'Book request submitted successfully! Your Request ID is: ' . $bookRequest->request_id);
    }

    /**
     * Display the specified resource.
     */
    public function show(BookRequest $bookRequest)
    {
        $user = Auth::user();
        
        // Students can only see their own requests
        if ($user->isStudent() && $bookRequest->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $bookRequest->load(['user', 'book.author', 'book.category', 'approver', 'fulfiller']);

        return view('book-requests.show', compact('bookRequest'));
    }

    /**
     * Download digital book for approved digital request
     */
    public function download(BookRequest $bookRequest)
    {
        $user = Auth::user();
        
        // Check if user is authorized
        if ($user->isStudent() && $bookRequest->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        // Check if request is for digital and approved
        if ($bookRequest->request_type !== 'digital') {
            abort(404, 'This request is not for a digital book.');
        }

        if ($bookRequest->status !== 'approved' && $bookRequest->status !== 'fulfilled') {
            abort(404, 'This request is not approved yet.');
        }

        $book = $bookRequest->book;

        // Check if book is digital
        if (!$book->isDigital()) {
            abort(404, 'This book is not available for download.');
        }

        if (!Storage::disk('public')->exists($book->file_path)) {
            abort(404, 'File not found.');
        }

        // Mark request as fulfilled if downloading
        if ($bookRequest->status === 'approved') {
            $bookRequest->update([
                'status' => 'fulfilled',
                'fulfilled_by' => $bookRequest->user_id, // Self-fulfilled for digital
                'fulfilled_at' => now(),
            ]);
        }

        return Storage::disk('public')->download($book->file_path, $book->file_name);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BookRequest $bookRequest)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $bookRequest->load(['user', 'book', 'approver', 'fulfiller']);

        return view('book-requests.edit', compact('bookRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BookRequest $bookRequest)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,fulfilled,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        $bookRequest->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'approved_by' => $validated['status'] === 'approved' ? $user->id : $bookRequest->approved_by,
            'approved_at' => $validated['status'] === 'approved' ? now() : $bookRequest->approved_at,
            'fulfilled_by' => $validated['status'] === 'fulfilled' ? $user->id : $bookRequest->fulfilled_by,
            'fulfilled_at' => $validated['status'] === 'fulfilled' ? now() : $bookRequest->fulfilled_at,
        ]);

        return redirect()->route('book-requests.index')
            ->with('status', 'Book request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookRequest $bookRequest)
    {
        $user = Auth::user();
        
        // Students can only delete their own pending requests
        if ($user->isStudent()) {
            if ($bookRequest->user_id !== $user->id) {
                abort(403, 'Access denied.');
            }
            if ($bookRequest->status !== 'pending') {
                abort(403, 'Only pending requests can be cancelled.');
            }
        } else {
            if (!$user->isAdmin()) {
                abort(403, 'Access denied. Admin privileges required.');
            }
        }

        $bookRequest->delete();

        return redirect()->route('book-requests.index')
            ->with('status', 'Book request deleted successfully.');
    }
}
