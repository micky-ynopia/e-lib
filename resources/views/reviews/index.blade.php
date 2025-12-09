@extends('layouts.app')

@section('title', 'Reviews - ' . $book->title)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Reviews for "{{ $book->title }}"</h5>
                        <small class="text-muted">by {{ $book->author?->last_name }}, {{ $book->author?->first_name }}</small>
                    </div>
                    <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Book
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($book->reviews_count > 0)
                <div class="mb-4 text-center p-3 bg-light rounded">
                    <div class="display-4 text-warning mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= round($book->average_rating) ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                    <h3>{{ number_format($book->average_rating, 1) }} out of 5</h3>
                    <p class="text-muted mb-0">{{ $book->reviews_count }} {{ Str::plural('review', $book->reviews_count) }}</p>
                </div>
                @endif

                @forelse($reviews as $review)
                <div class="border-bottom pb-4 mb-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>{{ $review->user->name }}</strong>
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                    </div>
                    @if($review->comment)
                    <p class="mb-0 mt-2">{{ $review->comment }}</p>
                    @else
                    <p class="text-muted mb-0 mt-2"><em>No comment provided</em></p>
                    @endif
                    
                    @auth
                    @if(auth()->user()->id === $review->user_id || auth()->user()->isAdmin())
                    <div class="mt-3">
                        @if(auth()->user()->id === $review->user_id)
                        <form method="POST" action="{{ route('reviews.destroy', $review) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete your review?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Delete My Review
                            </button>
                        </form>
                        @endif
                        
                        @if(auth()->user()->isAdmin() && !$review->is_approved)
                        <form method="POST" action="{{ route('reviews.approve', $review) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="bi bi-check"></i> Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('reviews.reject', $review) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-x"></i> Reject
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif
                    @endauth
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-chat-dots display-1 text-muted"></i>
                    <p class="text-muted mt-3">No reviews yet. Be the first to review this book!</p>
                    <a href="{{ route('books.show', $book) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Write a Review
                    </a>
                </div>
                @endforelse

                {{ $reviews->links() }}
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6>Book Information</h6>
                <p class="mb-2"><strong>Title:</strong> {{ $book->title }}</p>
                <p class="mb-2"><strong>Author:</strong> {{ $book->author?->last_name }}, {{ $book->author?->first_name }}</p>
                <p class="mb-2"><strong>Category:</strong> {{ $book->category?->name ?? 'Uncategorized' }}</p>
                <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-primary w-100 mt-3">
                    <i class="bi bi-book"></i> View Book Details
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

