@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h2 class="mb-2">{{ $book->title }}</h2>
                        <p class="text-muted mb-0">
                            <strong>Author:</strong> {{ $book->author?->last_name }}, {{ $book->author?->first_name }}
                        </p>
                    </div>
                    @auth
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    @endif
                    @endauth
                </div>

                @if($book->cover_image)
                <div class="text-center mb-4">
                    <img src="{{ Storage::url($book->cover_image) }}" 
                         alt="{{ $book->title }} cover" 
                         style="max-height: 400px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Category:</strong> {{ $book->category?->name ?? 'Uncategorized' }}</p>
                        <p><strong>ISBN:</strong> {{ $book->isbn }}</p>
                        <p><strong>Published Year:</strong> {{ $book->published_year ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Type:</strong> 
                            <span class="badge bg-{{ $book->book_type == 'digital' ? 'info' : ($book->book_type == 'both' ? 'primary' : 'secondary') }}">
                                {{ ucfirst($book->book_type ?? 'physical') }}
                            </span>
                        </p>
                        <p><strong>Stock:</strong> 
                            <span class="badge bg-{{ $book->available_copies > 0 ? 'success' : 'danger' }}">
                                {{ $book->available_copies }} / {{ $book->total_copies }} available
                            </span>
                        </p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-{{ $book->status == 'approved' ? 'success' : ($book->status == 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($book->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($book->description)
                <div class="mb-3">
                    <h5>Description</h5>
                    <p>{{ $book->description }}</p>
                </div>
                @endif

                <!-- Favorite Button -->
                @auth
                <div class="mb-3">
                    @if(auth()->user()->isFavorited($book))
                        <form method="POST" action="{{ route('books.unfavorite', $book) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-heart-fill"></i> Remove from Favorites
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('books.favorite', $book) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-heart"></i> Add to Favorites
                            </button>
                        </form>
                    @endif
                </div>
                @endauth

                <!-- Digital Book Actions -->
                @if($book->isDigital() && auth()->check())
                <div class="border-top pt-4 mb-3">
                    <h5 class="mb-3">Reading Options</h5>
                    @if($book->file_path)
                    <div class="d-flex gap-2">
                        <a href="{{ route('books.read', $book) }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-book"></i> Read Online
                        </a>
                        <a href="{{ route('books.download', $book) }}" class="btn btn-success btn-lg">
                            <i class="bi bi-download"></i> Download ({{ $book->formatted_file_size }})
                        </a>
                    </div>
                    <div class="mt-2 text-muted small">
                        <i class="bi bi-eye"></i> {{ number_format($book->view_count) }} views | 
                        <i class="bi bi-download"></i> {{ number_format($book->download_count) }} downloads
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> PDF file is not available yet. Please check back later or contact the librarian.
                    </div>
                    @endif
                </div>
                @endif

                <!-- Physical Book Actions -->
                @if($book->isPhysical() && auth()->check())
                <div class="border-top pt-4">
                    <h5>Borrowing Options</h5>
                    @if($book->available_copies > 0)
                    <p class="text-success"><i class="bi bi-check-circle"></i> Available for borrowing</p>
                    <p>This is a physical book. Please visit the library to borrow it.</p>
                    @else
                    <p class="text-danger mb-3"><i class="bi bi-x-circle"></i> Currently unavailable</p>
                    @if(!auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('reserves.store') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-calendar-plus"></i> Reserve This Book
                            </button>
                        </form>
                        <p class="text-muted small mt-2">Get notified when this book becomes available</p>
                    @endif
                    @endif
                    
                    @auth
                    @if(auth()->user()->isAdmin())
                    <div class="mt-3">
                        <a href="{{ route('books.queue', $book) }}" class="btn btn-outline-primary">
                            <i class="bi bi-list-ol"></i> View Reservation Queue
                        </a>
                    </div>
                    @endif
                    @endauth
                </div>
                @endif

                <!-- Reviews Section -->
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Reviews & Ratings</h5>
                        <div>
                            @if($book->reviews_count > 0)
                            <div class="d-flex align-items-center">
                                <span class="text-warning me-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= round($book->average_rating) ? '-fill' : '' }}"></i>
                                    @endfor
                                </span>
                                <strong>{{ number_format($book->average_rating, 1) }}</strong>
                                <span class="text-muted ms-2">({{ $book->reviews_count }} {{ Str::plural('review', $book->reviews_count) }})</span>
                            </div>
                            @else
                            <span class="text-muted">No reviews yet</span>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Review Form -->
                    @auth
                    @php
                        $userReview = \App\Models\Review::where('user_id', auth()->id())
                            ->where('book_id', $book->id)
                            ->first();
                    @endphp
                    @if(!$userReview)
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Write a Review</h6>
                            <form method="POST" action="{{ route('reviews.store', $book) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Rating</label>
                                    <div class="rating-input">
                                        @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" required>
                                        <label for="rating{{ $i }}" class="text-warning" style="cursor: pointer;">
                                            <i class="bi bi-star"></i>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="comment" class="form-label">Comment (optional)</label>
                                    <textarea name="comment" id="comment" class="form-control" rows="3" maxlength="1000" placeholder="Share your thoughts about this book..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <strong>Your Review:</strong>
                        <div class="mt-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $userReview->rating ? '-fill text-warning' : '' }}"></i>
                            @endfor
                            @if($userReview->comment)
                            <p class="mb-0 mt-1">{{ $userReview->comment }}</p>
                            @endif
                            @if(!$userReview->is_approved)
                            <small class="text-muted">(Pending approval)</small>
                            @endif
                        </div>
                    </div>
                    @endif
                    @endauth

                    <!-- Recent Reviews -->
                    @php
                        $reviews = $book->reviews()->approved()->with('user')->latest()->take(5)->get();
                    @endphp
                    @if($reviews->count() > 0)
                    <div class="reviews-list">
                        <h6 class="mb-3">Recent Reviews</h6>
                        @foreach($reviews as $review)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>{{ $review->user->name }}</strong>
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                            @if($review->comment)
                            <p class="mb-0">{{ $review->comment }}</p>
                            @endif
                        </div>
                        @endforeach
                        <a href="{{ route('reviews.index', $book) }}" class="btn btn-sm btn-outline-primary">
                            View All Reviews ({{ $book->reviews_count }})
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-clock-history"></i> Borrow History</h6>
            </div>
            <div class="card-body">
                @forelse ($book->borrows->take(10) as $borrow)
                <div class="border-bottom pb-2 mb-2">
                    <small class="text-muted">#{{ $borrow->id }}</small>
                    <div class="small">
                        <strong>{{ $borrow->user->name }}</strong><br>
                        <i class="bi bi-calendar"></i> {{ $borrow->borrowed_at->format('M d, Y') }} 
                        → {{ $borrow->returned_at?->format('M d, Y') ?? 'Not returned' }}
                        <br>
                        <span class="badge bg-{{ $borrow->status == 'returned' ? 'success' : 'warning' }}">
                            {{ ucfirst($borrow->status) }}
                        </span>
                    </div>
                </div>
                @empty
                    <p class="text-muted small">No borrow history yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('books.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Books
    </a>
</div>
@endsection

