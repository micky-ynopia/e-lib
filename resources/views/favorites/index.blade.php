@extends('layouts.app')

@section('title', 'My Favorites')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 m-0"><i class="bi bi-heart-fill text-danger"></i> My Favorites</h1>
    <a href="{{ route('books.index') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Browse More Books
    </a>
</div>

@if($favorites->isEmpty())
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> You haven't added any books to your favorites yet.
    <a href="{{ route('books.index') }}" class="alert-link">Start browsing books!</a>
</div>
@else
<div class="row g-4">
    @foreach($favorites as $favorite)
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">
                    <a href="{{ route('books.show', $favorite->book) }}" class="text-decoration-none">
                        {{ $favorite->book->title }}
                    </a>
                </h5>
                <p class="text-muted mb-2">
                    <i class="bi bi-person"></i> {{ $favorite->book->author->first_name }} {{ $favorite->book->author->last_name }}
                </p>
                @if($favorite->book->category)
                <p class="text-muted mb-2">
                    <ema class="bi bi-folder"></i> {{ $favorite->book->category->name }}
                </p>
                @endif
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('books.show', $favorite->book) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> View Details
                    </a>
                    @if($favorite->book->isDigital())
                    <a href="{{ route('books.read', $favorite->book) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-book"></i> Read
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-footer bg-light">
                <form method="POST" action="{{ route('books.unfavorite', $favorite->book) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-heart-break"></i> Remove
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-4">
    {{ $favorites->links() }}
</div>
@endif
@endsection

