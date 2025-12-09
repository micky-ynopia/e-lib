@extends('layouts.app')

@section('title', 'Book Requests')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Book Requests</h1>
    @if(auth()->user()->isStudent())
        <a href="{{ route('book-requests.create') }}" class="btn btn-success">New Request</a>
    @endif
</div>

@if(auth()->user()->isStudent())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        Your book requests will be reviewed by library staff. You'll receive email notifications about the status.
    </div>
@endif

<table class="table table-hover align-middle">
    <thead>
        <tr>
            @if(auth()->user()->isAdmin())
                <th>Student</th>
            @endif
            <th>Book</th>
            <th>Request ID</th>
            <th>Type</th>
            <th>Status</th>
            <th>Requested</th>
            @if(auth()->user()->isAdmin())
                <th>Actions</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse ($bookRequests as $request)
            <tr>
                @if(auth()->user()->isAdmin())
                    <td>
                        <div>
                            <strong>{{ $request->user->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $request->user->student_id }} - {{ $request->user->course }}</small>
                        </div>
                    </td>
                @endif
                <td>
                    <div>
                        <strong>{{ $request->book->title }}</strong>
                        <br>
                        <small class="text-muted">{{ $request->book->author->first_name }} {{ $request->book->author->last_name }}</small>
                    </div>
                </td>
                <td>
                    <code>{{ $request->request_id }}</code>
                </td>
                <td>
                    <span class="badge {{ $request->request_type === 'physical' ? 'bg-primary' : 'bg-info' }}">
                        {{ ucfirst($request->request_type) }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $request->status_badge_class }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </td>
                <td>
                    {{ $request->requested_at->format('M d, Y') }}
                    <br>
                    <small class="text-muted">{{ $request->requested_at->format('h:i A') }}</small>
                </td>
                @if(auth()->user()->isAdmin())
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('book-requests.show', $request) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            <a href="{{ route('book-requests.edit', $request) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            @if($request->status === 'pending')
                                <form method="POST" action="{{ route('book-requests.update', $request) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                            @endif
                        </div>
                    </td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="{{ auth()->user()->isAdmin() ? '7' : '5' }}" class="text-muted text-center py-4">
                    @if(auth()->user()->isStudent())
                        No book requests yet. <a href="{{ route('book-requests.create') }}">Make your first request</a>!
                    @else
                        No book requests found.
                    @endif
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">{{ $bookRequests->links() }}</div>
@endsection
