@extends('layouts.app')

@section('title', 'Overdue Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Overdue Borrows Report</h1>
    <div>
        <a href="{{ route('reports.overdue.export', request()->all()) }}" class="btn btn-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
        <a href="{{ route('reports.statistics') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

@if($overdueBorrows->count() > 0)
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <strong>{{ $overdueBorrows->total() }} items are overdue</strong>
    </div>
@endif

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Borrow ID</th>
                <th>User</th>
                <th>Book</th>
                <th>Borrowed</th>
                <th>Due Date</th>
                <th>Days Overdue</th>
                <th>Fine Amount</th>
                <th>Fine Paid</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($overdueBorrows as $borrow)
                <tr class="{{ $borrow->days_overdue > 30 ? 'table-danger' : ($borrow->days_overdue > 14 ? 'table-warning' : '') }}">
                    <td>#{{ $borrow->id }}</td>
                    <td>{{ $borrow->user->name }}</td>
                    <td>{{ $borrow->book->title }}</td>
                    <td>{{ $borrow->borrowed_at->format('M d, Y') }}</td>
                    <td>{{ $borrow->due_at->format('M d, Y') }}</td>
                    <td><strong class="text-danger">{{ $borrow->days_overdue }}</strong> days</td>
                    <td>
                        @if($borrow->fine_amount && $borrow->fine_amount > 0)
                            <strong class="text-danger">₱{{ number_format($borrow->fine_amount, 2) }}</strong>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($borrow->fine_paid_at)
                            <span class="badge bg-success">Paid</span>
                        @else
                            <span class="badge bg-warning">Unpaid</span>
                        @endif
                    </td>
                    <td><span class="badge bg-danger">Overdue</span></td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No overdue borrows! 🎉</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $overdueBorrows->links() }}</div>
@endsection

