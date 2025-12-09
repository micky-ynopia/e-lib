@extends('layouts.app')

@section('title', 'Borrows')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Borrows</h1>
    @auth
    @if(auth()->user()->isAdmin())
    <a href="{{ route('borrows.create') }}" class="btn btn-success">New Borrow</a>
    @endif
    @endauth
    </div>

<table class="table table-hover align-middle">
    <thead>
        <tr>
            <th>User</th>
            <th>Book</th>
            <th>Dates</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($borrows as $borrow)
            <tr>
                <td>{{ $borrow->user?->name }}</td>
                <td>{{ $borrow->book?->title }}</td>
                <td>
                    {{ $borrow->borrowed_at->format('Y-m-d') }} → {{ $borrow->due_at->format('Y-m-d') }}
                    @if ($borrow->returned_at)
                        <div class="small text-muted">Returned: {{ $borrow->returned_at->format('Y-m-d') }}</div>
                    @endif
                </td>
                <td class="text-capitalize">{{ $borrow->status }}</td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="{{ route('borrows.show', $borrow) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        @auth
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('borrows.edit', $borrow) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('borrows.destroy', $borrow) }}" onsubmit="return confirm('Delete this borrow?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                        @endif
                        @endauth
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-muted">No borrows yet.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">{{ $borrows->links() }}</div>
@endsection


