<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FavoriteController extends Controller
{
    /**
     * Display user's favorite books
     */
    public function index()
    {
        $user = Auth::user();
        
        $favorites = Cache::remember("user_{$user->id}_favorites", 300, function () use ($user) {
            return $user->favorites()->with(['book.author', 'book.category'])->paginate(12);
        });
        
        return view('favorites.index', compact('favorites'));
    }

    /**
     * Add book to favorites
     */
    public function store(Request $request, Book $book)
    {
        $user = Auth::user();
        
        // Check if already favorited
        if ($user->isFavorited($book)) {
            return back()->with('status', 'Book is already in your favorites!');
        }
        
        Favorite::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // Clear cache
        Cache::forget("user_{$user->id}_favorites");

        return back()->with('status', 'Book added to favorites! ✅');
    }

    /**
     * Remove book from favorites
     */
    public function destroy(Book $book)
    {
        $user = Auth::user();
        
        $favorite = Favorite::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            
            // Clear cache
            Cache::forget("user_{$user->id}_favorites");
            
            return back()->with('status', 'Book removed from favorites!');
        }

        return back()->with('error', 'Book not found in favorites.');
    }

    /**
     * Toggle favorite status (AJAX-friendly)
     */
    public function toggle(Request $request, Book $book)
    {
        $user = Auth::user();
        $isFavorited = $user->isFavorited($book);

        if ($isFavorited) {
            $this->destroy($book);
            return response()->json(['status' => 'removed', 'message' => 'Removed from favorites']);
        } else {
            $this->store($request, $book);
            return response()->json(['status' => 'added', 'message' => 'Added to favorites']);
        }
    }
}
