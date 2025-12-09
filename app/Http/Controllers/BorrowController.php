<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $borrows = Borrow::with(['user','book'])->orderByDesc('id')->paginate(10);
        return view('borrows.index', compact('borrows'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403);
        }
        $users = User::orderBy('name')->get();
        $books = Book::where('available_copies', '>', 0)->orderBy('title')->get();
        return view('borrows.create', compact('users','books'));
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
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'borrowed_at' => 'required|date',
            'due_at' => 'required|date|after_or_equal:borrowed_at',
        ]);

        $book = Book::lockForUpdate()->findOrFail($validated['book_id']);
        if ($book->available_copies < 1) {
            return back()->withErrors(['book_id' => 'Selected book has no available copies'])->withInput();
        }

        $borrow = new Borrow($validated);
        $borrow->status = 'borrowed';

        \DB::transaction(function () use ($borrow, $book) {
            $borrow->save();
            $book->decrement('available_copies');
        });

        return redirect()->route('borrows.index')->with('status', 'Borrow recorded.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrow $borrow)
    {
        $borrow->load(['user','book']);
        return view('borrows.show', compact('borrow'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Borrow $borrow)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403);
        }
        $borrow->load(['user','book']);
        return view('borrows.edit', compact('borrow'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Borrow $borrow)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403);
        }
        $validated = $request->validate([
            'borrowed_at' => 'required|date',
            'due_at' => 'required|date|after_or_equal:borrowed_at',
            'returned_at' => 'nullable|date|after_or_equal:borrowed_at',
            'status' => 'required|in:borrowed,returned,overdue',
        ]);

        $wasBorrowed = $borrow->status === 'borrowed' && $borrow->returned_at === null;
        $becomesReturned = $validated['status'] === 'returned' && ($borrow->returned_at === null && empty($validated['returned_at']));

        \DB::transaction(function () use ($borrow, $validated, $wasBorrowed, $becomesReturned) {
            $borrow->fill($validated);
            if ($becomesReturned) {
                $borrow->returned_at = now();
            }
            $borrow->save();

            if ($wasBorrowed && $borrow->status === 'returned') {
                // increment stock once when moving to returned
                $borrow->book()->update(['available_copies' => \DB::raw('available_copies + 1')]);
            }
        });

        return redirect()->route('borrows.index')->with('status', 'Borrow updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Borrow $borrow)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403);
        }
        $shouldRestock = $borrow->status === 'borrowed' && $borrow->returned_at === null;
        \DB::transaction(function () use ($borrow, $shouldRestock) {
            if ($shouldRestock) {
                $borrow->book()->update(['available_copies' => \DB::raw('available_copies + 1')]);
            }
            $borrow->delete();
        });
        return redirect()->route('borrows.index')->with('status', 'Borrow deleted.');
    }

    /**
     * Pay fine for an overdue borrow
     */
    public function payFine(Request $request, Borrow $borrow)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        if (!$borrow->fine_amount || $borrow->fine_amount <= 0) {
            return back()->withErrors(['error' => 'No fine to pay for this borrow.']);
        }

        if ($borrow->fine_paid_at) {
            return back()->withErrors(['error' => 'Fine has already been paid.']);
        }

        $validated = $request->validate([
            'fine_notes' => 'nullable|string|max:500',
        ]);

        $borrow->update([
            'fine_paid_at' => now(),
            'fine_notes' => $validated['fine_notes'] ?? null,
        ]);

        return back()->with('status', "Fine of ₱" . number_format($borrow->fine_amount, 2) . " paid successfully!");
    }
}
