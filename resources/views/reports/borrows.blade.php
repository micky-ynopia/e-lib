@extends('layouts.app')

@section('title', 'Borrows Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Borrows Report</h1>
    <div>
        <a href="{{ route('reports.borrows.export', request()->all()) }}" class="btn btn-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
        <a href="{{ route('reports.statistics') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-3">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All</option>
                    <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Borrow ID</th>
                <th>User</th>
                <th>Book</th>
                <th>Borrowed</th>
                <th>Due Date</th>
                <th>Returned</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($borrows as $borrow)
                <tr>
                    <td>#{{ $borrow->id }}</td>
                    <td>{{ $borrow->user->name }}</td>
                    <td>{{ $borrow->book->title }}</td>
                    <td>{{ $borrow->borrowed_at->format('M d, Y') }}</td>
                    <td>{{ $borrow->due_at->format('M d, Y') }}</td>
                    <td>{{ $borrow->returned_at?->format('M d, Y') ?? '—' }}</td>
                    <td>
                        <span class="badge bg-{{ $borrow->status == 'returned' ? 'success' : ($borrow->status == 'overdue' ? 'danger' : 'warning') }}">
                            {{ ucfirst($borrow->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No borrows found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $borrows->links() }}</div>
@endsection

