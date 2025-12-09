@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 m-0">Admin Dashboard</h1>
            <span class="badge bg-danger">Librarian</span>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-2 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3 class="card-title">{{ $stats['total_books'] }}</h3>
                <p class="card-text">Total Books</p>
            </div>
        </div>
    </div>

    <div class="col-md-2 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3 class="card-title">{{ $stats['total_users'] }}</h3>
                <p class="card-text">Total Users</p>
            </div>
        </div>
    </div>

    <div class="col-md-2 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3 class="card-title">{{ $stats['total_borrows'] }}</h3>
                <p class="card-text">Total Borrows</p>
            </div>
        </div>
    </div>

    <div class="col-md-2 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3 class="card-title">{{ $stats['pending_requests'] }}</h3>
                <p class="card-text">Pending Requests</p>
            </div>
        </div>
    </div>

    <div class="col-md-2 mb-4">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h3 class="card-title">{{ $stats['overdue_borrows'] }}</h3>
                <p class="card-text">Overdue</p>
            </div>
        </div>
    </div>

    <div class="col-md-2 mb-4">
        <div class="card bg-secondary text-white">
            <div class="card-body text-center">
                <h3 class="card-title">{{ $stats['pending_approvals'] }}</h3>
                <p class="card-text">Pending Approvals</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Most Borrowed Books -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Most Borrowed Books</h5>
            </div>
            <div class="card-body">
                @forelse($mostBorrowedBooks as $book)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $book->title }}</h6>
                            <small class="text-muted">{{ $book->author->first_name }} {{ $book->author->last_name }}</small>
                        </div>
                        <span class="badge bg-primary">{{ $book->borrows_count }} borrows</span>
                    </div>
                @empty
                    <p class="text-muted">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Announcements -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Announcements</h5>
            </div>
            <div class="card-body">
                @forelse($recentAnnouncements as $announcement)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $announcement->title }}</h6>
                            <small class="text-muted">{{ $announcement->created_at->format('M d, Y') }}</small>
                        </div>
                        <span class="badge {{ $announcement->priority_badge_class }}">
                            {{ ucfirst($announcement->priority) }}
                        </span>
                    </div>
                @empty
                    <p class="text-muted">No announcements</p>
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
                        <a href="{{ route('announcements.create') }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-megaphone"></i> New Announcement
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
