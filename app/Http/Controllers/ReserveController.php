<?php

namespace App\Http\Controllers;

use App\Models\Reserve;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ReserveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $reserves = Reserve::with(['user', 'book'])->latest()->paginate(15);
        } else {
            $reserves = Auth::user()->reserves()->with('book')->latest()->paginate(15);
        }

        return view('reserves.index', compact('reserves'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $books = Book::where('book_type', '!=', 'digital')
            ->orderBy('title')
            ->get();
        
        return view('reserves.create', compact('books'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        
        // Check if user already has a pending reservation for this book
        $existingReserve = Auth::user()->reserves()
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'available'])
            ->first();

        if ($existingReserve) {
            return back()->withErrors(['book_id' => 'You already have a reservation for this book.'])->withInput();
        }

        // Create reservation
        $reserve = Reserve::create([
            'user_id' => Auth::id(),
            'book_id' => $validated['book_id'],
            'reserved_at' => now(),
            'expires_at' => now()->addDays(7),
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        // If book is available, notify user immediately
        if ($book->available_copies > 0) {
            $reserve->update(['status' => 'available', 'notified_at' => now()]);
            
            return redirect()->route('reserves.index')
                ->with('status', "Book is available! Please visit the library to borrow it within 7 days.");
        }

        return redirect()->route('reserves.index')
            ->with('status', 'Reservation created successfully. You will be notified when the book becomes available.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reserve $reserve)
    {
        $reserve->load(['user', 'book']);
        return view('reserves.show', compact('reserve'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reserve $reserve)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,available,fulfilled,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        $reserve->update($validated);

        if ($validated['status'] === 'available' && !$reserve->notified_at) {
            $reserve->update(['notified_at' => now()]);
            // TODO: Send email notification
        }

        return back()->with('status', 'Reservation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserve $reserve)
    {
        // Users can only cancel their own reservations
        if (!Auth::user()->isAdmin() && $reserve->user_id !== Auth::id()) {
            abort(403);
        }

        // Can only cancel pending reservations
        if ($reserve->status !== 'pending' && !Auth::user()->isAdmin()) {
            return back()->withErrors(['error' => 'Can only cancel pending reservations.']);
        }

        $reserve->delete();

        return redirect()->route('reserves.index')
            ->with('status', 'Reservation cancelled successfully.');
    }

    /**
     * Fulfill a reservation (mark as fulfilled when book is borrowed)
     */
    public function fulfill(Reserve $reserve)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $reserve->update([
            'status' => 'fulfilled',
        ]);

        return back()->with('status', 'Reservation fulfilled successfully.');
    }

    /**
     * Admin: List all pending reservations for a book
     */
    public function queue(Book $book)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $reserves = Reserve::where('book_id', $book->id)
            ->whereIn('status', ['pending', 'available'])
            ->with('user')
            ->orderBy('reserved_at')
            ->get();

        return view('reserves.queue', compact('book', 'reserves'));
    }
}
