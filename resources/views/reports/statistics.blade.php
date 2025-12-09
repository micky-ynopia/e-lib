@extends('layouts.app')

@section('title', 'Statistics Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">System Statistics</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
</div>

<div class="row g-3 mb-4">
    <!-- Books Statistics -->
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-book"></i> Books</h5>
            </div>
            <div class="card-body">
                <h2 class="text-primary">{{ $stats['total_books'] }}</h2>
                <p class="mb-0">
                    <small>Physical: {{ $stats['total_physical_books'] }} | Digital: {{ $stats['total_digital_books'] }}</small>
                </p>
            </div>
        </div>
    </div>

    <!-- Users Statistics -->
    <div class="col-md-4">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-people"></i> Users</h5>
            </div>
            <div class="card-body">
                <h2 class="text-info">{{ $stats['total_users'] }}</h2>
                <p class="mb-0">
                    <small>Students: {{ $stats['total_students'] }} | Staff: {{ $stats['total_staff'] }} | Librarians: {{ $stats['total_librarians'] }}</small>
                </p>
            </div>
        </div>
    </div>

    <!-- Borrows Statistics -->
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-arrow-left-right"></i> Borrows</h5>
            </div>
            <div class="card-body">
                <h2 class="text-success">{{ $stats['total_borrows'] }}</h2>
                <p class="mb-0">
                    <small>Active: {{ $stats['active_borrows'] }} | Overdue: {{ $stats['overdue_borrows'] }} | Returned: {{ $stats['returned_borrows'] }}</small>
                </p>
            </div>
        </div>
    </div>

    <!-- Authors Statistics -->
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Authors</h5>
            </div>
            <div class="card-body">
                <h2 class="text-warning">{{ $stats['total_authors'] }}</h2>
            </div>
        </div>
    </div>

    <!-- Categories Statistics -->
    <div class="col-md-4">
        <div class="card border-secondary">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-tags"></i> Categories</h5>
            </div>
            <div class="card-body">
                <h2 class="text-secondary">{{ $stats['total_categories'] }}</h2>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Pending</h5>
            </div>
            <div class="card-body">
                <h2 class="text-danger">{{ $stats['pending_approvals'] }}</h2>
                <p class="mb-0"><small>User Approvals</small></p>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Statistics -->
<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Book Requests</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Total Requests:</th>
                        <td class="text-end">{{ $stats['total_book_requests'] }}</td>
                    </tr>
                    <tr>
                        <th>Pending:</th>
                        <td class="text-end"><span class="badge bg-warning">{{ $stats['pending_requests'] }}</span></td>
                    </tr>
                    <tr>
                        <th>Approved:</th>
                        <td class="text-end"><span class="badge bg-info">{{ $stats['approved_requests'] }}</span></td>
                    </tr>
                    <tr>
                        <th>Fulfilled:</th>
                        <td class="text-end"><span class="badge bg-success">{{ $stats['fulfilled_requests'] }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Other Resources</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Total Theses:</th>
                        <td class="text-end">{{ $stats['total_theses'] }}</td>
                    </tr>
                    <tr>
                        <th>Approved Theses:</th>
                        <td class="text-end"><span class="badge bg-success">{{ $stats['approved_theses'] }}</span></td>
                    </tr>
                    <tr>
                        <th>Total Announcements:</th>
                        <td class="text-end">{{ $stats['total_announcements'] }}</td>
                    </tr>
                    <tr>
                        <th>Published:</th>
                        <td class="text-end"><span class="badge bg-primary">{{ $stats['published_announcements'] }}</span></td>
                    </tr>
                    <tr>
                        <th>Monthly Borrows:</th>
                        <td class="text-end"><strong>{{ $stats['monthly_borrows'] }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="card mt-3">
    <div class="card-header">
        <h5 class="mb-0">Quick Actions</h5>
    </div>
    <div class="card-body">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('reports.borrows') }}" class="btn btn-outline-primary">Borrows Report</a>
            <a href="{{ route('reports.books') }}" class="btn btn-outline-primary">Books Report</a>
            <a href="{{ route('reports.overdue') }}" class="btn btn-outline-danger">Overdue Report</a>
            <a href="{{ route('reports.popular-books') }}" class="btn btn-outline-success">Popular Books</a>
            <a href="{{ route('users.index', ['approval_status' => 'pending']) }}" class="btn btn-outline-warning">Pending Approvals</a>
        </div>
    </div>
</div>
@endsection

