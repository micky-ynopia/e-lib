@extends('layouts.app')

@section('title', 'Popular Books')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Popular Books Report</h1>
    <a href="{{ route('reports.statistics') }}" class="btn btn-secondary">Back</a>
</div>

<p class="text-muted">Showing the {{ $books->count() }} most borrowed books{{ $days > 0 ? ' (Last ' . $days . ' days)' : ' (All time)' }}</p>

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th width="60">Rank</th>
                <th>Book Title</th>
                <th>Author</th>
                <th>Category</th>
                <th class="text-center">Total Borrows</th>
                @if($days > 0)
                    <th class="text-center">Recent ({{ $days }}d)</th>
                @endif
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $index => $book)
                <tr>
                    <td>
                        <span class="badge bg-{{ $index < 3 ? 'primary' : 'secondary' }}">
                            #{{ $index + 1 }}
                        </span>
                    </td>
                    <td><strong>{{ $book->title }}</strong></td>
                    <td>{{ $book->author->last_name ?? '—' }}, {{ $book->author->first_name ?? '—' }}</td>
                    <td>{{ $book->category->name ?? '—' }}</td>
                    <td class="text-center">
                        <strong class="text-primary">{{ $book->borrows_count }}</strong>
                    </td>
                    @if($days > 0)
                        <td class="text-center">
                            {{ $recentBorrowsCount[$book->id] ?? 0 }}
                        </td>
                    @endif
                    <td><span class="badge bg-{{ $book->available_copies > 0 ? 'success' : 'danger' }}">{{ $book->available_copies }} / {{ $book->total_copies }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($books->isEmpty())
    <div class="alert alert-info">
        No borrowing data available yet.
    </div>
@endif
@endsection

