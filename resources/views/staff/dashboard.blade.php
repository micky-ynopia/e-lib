@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 m-0">Staff Dashboard</h1>
            <span class="badge bg-info">Library Staff</span>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pending Requests -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Pending Book Requests</h5>
            </div>
            <div class="card-body">
                @forelse($pendingRequests as $request)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $request->book->title }}</h6>
                            <small class="text-muted">
                                Requested by: {{ $request->user->name }} ({{ $request->request_id }})
                            </small>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('book-requests.show', $request) }}" class="btn btn-outline-primary">View</a>
                            <a href="{{ route('book-requests.edit', $request) }}" class="btn btn-outline-success">Approve</a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No pending requests</p>
                @endforelse
            </div>
        </div>
    </div>

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
                                Borrowed by: {{ $borrow->user->name }}
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
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('books.create') }}" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Add Book
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('authors.create') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-person-plus"></i> Add Author
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('book-requests.index') }}" class="btn btn-outline-warning w-100">
                            <i class="bi bi-list-check"></i> Manage Requests
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('borrows.index') }}" class="btn btn-outline-info w-100">
                            <i class="bi bi-book"></i> Manage Borrows
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('books.index') }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-search"></i> Browse Books
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('theses.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-file-text"></i> Manage Theses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
