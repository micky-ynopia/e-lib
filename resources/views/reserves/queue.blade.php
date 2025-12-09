@extends('layouts.app')

@section('title', 'Reservation Queue')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 m-0">
        <i class="bi bi-list-ol"></i> Reservation Queue
    </h1>
    <a href="{{ route('books.show', $book) }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Back to Book
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5>{{ $book->title }}</h5>
        <p class="text-muted mb-0">{{ $book->author->first_name }} {{ $book->author->last_name }}</p>
        <p class="mt-2 mb-0">
            Available Copies: 
            <span class="badge bg-{{ $book->available_copies > 0 ? 'success' : 'danger' }}">
                {{ $book->available_copies }} / {{ $book->total_copies }}
            </span>
        </p>
    </div>
</div>

<h5 class="mb-3">Queue ({{ $reserves->count() }} reservations)</h5>

@forelse($reserves as $index => $reserve)
<div class="card mb-2">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-1 text-center">
                <span class="badge bg-primary fs-5">#{{ $index + 1 }}</span>
            </div>
            <div class="col-md-5">
                <strong>{{ $reserve->user->name }}</strong><br>
                <small class="text-muted">{{ $reserve->user->email }}</small>
            </div>
            <div class="col-md-3">
                <small class="text-muted">Reserved: {{ $reserve->reserved_at->format('M d, Y') }}</small><br>
                @if($reserve->notified_at)
                    <small class="text-success"><i class="bi bi-check-circle"></i> Notified</small>
                @endif
            </div>
            <div class="col-md-3发行-end">
                @if($reserve->status === 'available')
                <form method="POST" action="{{ route('reserves.fulfill', $reserve) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-check"></i> Mark Fulfilled
                    </button>
                </form>
                @endif
                <span class="badge bg-{{ 
                    $reserve->status === 'available' ? 'success' : 'warning'
                }} ms-2">
                    {{ ucfirst($reserve->status) }}
                </span>
            </div>
        </div>
    </div>
</div>
@empty
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> No reservations in queue for this book.
</div>
@endforelse

@endsection

