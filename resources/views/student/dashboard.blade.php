@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 m-0">Welcome, {{ auth()->user()->name }}!</h1>
            <span class="badge bg-primary">{{ auth()->user()->role_display }}</span>
        </div>

        @if(!auth()->user()->is_approved)
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                Your account is pending approval from library staff. You can browse books but cannot make requests yet.
            </div>
        @endif
    </div>
</div>

<div class="row">
    <!-- Quick Stats -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $recentBorrows->count() }}</h4>
                        <p class="card-text">Recent Borrows</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-book" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $pendingRequests->count() }}</h4>
                        <p class="card-text">Pending Requests</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-clock" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Available Books</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-bookmark-check" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Digital Books</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-file-earmark-pdf" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Borrows -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Borrows</h5>
            </div>
            <div class="card-body">
                @forelse($recentBorrows as $borrow)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $borrow->book->title }}</h6>
                            <small class="text-muted">
                                Due: {{ $borrow->due_at->format('M d, Y') }}
                            </small>
                        </div>
                        <span class="badge {{ $borrow->status === 'borrowed' ? 'bg-warning' : 'bg-success' }}">
                            {{ ucfirst($borrow->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-muted">No recent borrows</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pending Requests -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Pending Requests</h5>
            </div>
            <div class="card-body">
                @forelse($pendingRequests as $request)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $request->book->title }}</h6>
                            <small class="text-muted">
                                Request ID: {{ $request->request_id }}
                            </small>
                        </div>
                        <span class="badge {{ $request->status_badge_class }}">
                            {{ ucfirst($request->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-muted">No pending requests</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('books.index') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-search"></i> Browse Books
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('book-requests.create') }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-plus-circle"></i> Request Book
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('borrows.index') }}" class="btn btn-outline-info w-100">
                            <i class="bi bi-list-check"></i> My Borrows
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('theses.index') }}" class="btn btn-outline-warning w-100">
                            <i class="bi bi-file-text"></i> Thesis Repository
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
