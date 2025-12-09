@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">User Details</h1>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Back to Users</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Name:</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Role:</th>
                        <td><span class="badge bg-info">{{ $user->role_display }}</span></td>
                    </tr>
                    <tr>
                        <th>Approval Status:</th>
                        <td>
                            @if($user->is_approved)
                                <span class="badge bg-success">Approved</span>
                                <small class="text-muted">on {{ $user->approved_at?->format('M d, Y') }}</small>
                            @else
                                <span class="badge bg-warning">Pending Approval</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Student Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Student ID:</th>
                        <td>{{ $user->student_id ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Course:</th>
                        <td>{{ $user->course ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Year Level:</th>
                        <td>{{ $user->year_level ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>{{ $user->phone ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Borrow History</h5>
                <span class="badge bg-secondary">{{ $user->borrows->count() }} total</span>
            </div>
            <div class="card-body">
                @if($user->borrows->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th>Borrowed</th>
                                    <th>Due</th>
                                    <th>Returned</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->borrows->take(10) as $borrow)
                                    <tr>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No borrows yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Book Requests</h5>
                <span class="badge bg-secondary">{{ $user->bookRequests->count() }} total</span>
            </div>
            <div class="card-body">
                @if($user->bookRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Book</th>
                                    <th>Type</th>
                                    <th>Requested</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->bookRequests->take(10) as $request)
                                    <tr>
                                        <td><code>{{ $request->request_id }}</code></td>
                                        <td>{{ $request->book->title }}</td>
                                        <td><span class="badge bg-secondary">{{ ucfirst($request->request_type) }}</span></td>
                                        <td>{{ $request->requested_at->format('M d, Y') }}</td>
                                        <td><span class="badge bg-{{ $request->status_badge_class }}">{{ ucfirst($request->status) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No requests yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit User</a>
    @if(!$user->is_approved)
        <form method="POST" action="{{ route('users.approve', $user) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success">Approve User</button>
        </form>
    @else
        <form method="POST" action="{{ route('users.reject', $user) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-warning">Revoke Approval</button>
        </form>
    @endif
</div>
@endsection

