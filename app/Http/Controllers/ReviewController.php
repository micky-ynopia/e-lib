<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    /**
     * Display reviews for a book
     */
    public function index(Book $book)
    {
        $reviews = Review::where('book_id', $book->id)
            ->approved()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('reviews.index', compact('book', 'reviews'));
    }

    /**
     * Store a newly created review
     */
    public function store(Request $request, Book $book)
    {
        $user = Auth::user();

        // Check if user already reviewed this book
        $existingReview = Review::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        if ($existingReview) {
            return back()->withErrors(['error' => 'You have already reviewed this book.'])->withInput();
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Auto-approve for staff/librarians, require approval for students
        $isApproved = $user->isAdmin();

        $review = Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_approved' => $isApproved,
        ]);

        // Clear book reviews cache
        Cache::forget("book_{$book->id}_reviews");
        Cache::forget("book_{$book->id}_rating");

        $message = $isApproved 
            ? 'Review submitted successfully!' 
            : 'Review submitted and pending approval.';

        return back()->with('status', $message);
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, Review $review)
    {
        $user = Auth::user();

        // Users can only update their own reviews
        if ($review->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        // If admin updates, auto-approve
        if ($user->isAdmin() && !$review->is_approved) {
            $review->update(['is_approved' => true]);
        }

        // Clear cache
        Cache::forget("book_{$review->book_id}_reviews");
        Cache::forget("book_{$review->book_id}_rating");

        return back()->with('status', 'Review updated successfully!');
    }

    /**
     * Remove the specified review
     */
    public function destroy(Review $review)
    {
        $user = Auth::user();

        // Users can only delete their own reviews, or admins can delete any
        if ($review->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $bookId = $review->book_id;
        $review->delete();

        // Clear cache
        Cache::forget("book_{$bookId}_reviews");
        Cache::forget("book_{$bookId}_rating");

        return back()->with('status', 'Review deleted successfully!');
    }

    /**
     * Approve a review (admin only)
     */
    public function approve(Review $review)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $review->update(['is_approved' => true]);

        // Clear cache
        Cache::forget("book_{$review->book_id}_reviews");
        Cache::forget("book_{$review->book_id}_rating");

        return back()->with('status', 'Review approved successfully!');
    }

    /**
     * Reject a review (admin only)
     */
    public function reject(Review $review)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $review->update(['is_approved' => false]);

        // Clear cache
        Cache::forget("book_{$review->book_id}_reviews");
        Cache::forget("book_{$review->book_id}_rating");

        return back()->with('status', 'Review rejected.');
    }
}

