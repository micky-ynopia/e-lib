@extends('layouts.app')

@section('title', 'Book Request Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Book Request Details</h5>
                    <span class="badge {{ $bookRequest->status_badge_class }} fs-6">
                        {{ ucfirst($bookRequest->status) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Request Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Request ID:</strong></td>
                                <td><code>{{ $bookRequest->request_id }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Request Type:</strong></td>
                                <td>
                                    <span class="badge {{ $bookRequest->request_type === 'physical' ? 'bg-primary' : 'bg-info' }}">
                                        {{ ucfirst($bookRequest->request_type) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Requested Date:</strong></td>
                                <td>{{ $bookRequest->requested_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            @if($bookRequest->approved_at)
                                <tr>
                                    <td><strong>Approved Date:</strong></td>
                                    <td>{{ $bookRequest->approved_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            @endif
                            @if($bookRequest->fulfilled_at)
                                <tr>
                                    <td><strong>Fulfilled Date:</strong></td>
                                    <td>{{ $bookRequest->fulfilled_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Student Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $bookRequest->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Student ID:</strong></td>
                                <td>{{ $bookRequest->user->student_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Course:</strong></td>
                                <td>{{ $bookRequest->user->course }}</td>
                            </tr>
                            <tr>
                                <td><strong>Year Level:</strong></td>
                                <td>{{ $bookRequest->user->year_level }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <h6>Book Information</h6>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $bookRequest->book->title }}</h5>
                                <p class="card-text">
                                    <strong>Author:</strong> {{ $bookRequest->book->author->first_name }} {{ $bookRequest->book->author->last_name }}<br>
                                    <strong>Category:</strong> {{ $bookRequest->book->category->name ?? 'N/A' }}<br>
                                    <strong>ISBN:</strong> {{ $bookRequest->book->isbn }}<br>
                                    <strong>Published Year:</strong> {{ $bookRequest->book->published_year ?? 'N/A' }}<br>
                                    <strong>Available Copies:</strong> {{ $bookRequest->book->available_copies }} / {{ $bookRequest->book->total_copies }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($bookRequest->notes)
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6>Notes</h6>
                            <p class="text-muted">{{ $bookRequest->notes }}</p>
                        </div>
                    </div>
                @endif

                @if($bookRequest->approver)
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6>Approval Information</h6>
                            <p class="text-muted">
                                <strong>Approved by:</strong> {{ $bookRequest->approver->name }}<br>
                                <strong>Approved on:</strong> {{ $bookRequest->approved_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                @endif

                @if($bookRequest->fulfiller)
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6>Fulfillment Information</h6>
                            <p class="text-muted">
                                <strong>Fulfilled by:</strong> {{ $bookRequest->fulfiller->name }}<br>
                                <strong>Fulfilled on:</strong> {{ $bookRequest->fulfilled_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Actions</h6>
            </div>
            <div class="card-body">
                @if(auth()->user()->isAdmin())
                    <div class="d-grid gap-2">
                        @if($bookRequest->status === 'pending')
                            <form method="POST" action="{{ route('book-requests.update', $bookRequest) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-success w-100">Approve Request</button>
                            </form>
                            <form method="POST" action="{{ route('book-requests.update', $bookRequest) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-danger w-100">Reject Request</button>
                            </form>
                        @elseif($bookRequest->status === 'approved')
                            <form method="POST" action="{{ route('book-requests.update', $bookRequest) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="fulfilled">
                                <button type="submit" class="btn btn-primary w-100">Mark as Fulfilled</button>
                            </form>
                        @endif
                        <a href="{{ route('book-requests.edit', $bookRequest) }}" class="btn btn-outline-primary w-100">Edit Request</a>
                    </div>
                @else
                    <div class="d-grid gap-2">
                        @if($bookRequest->status === 'pending')
                            <form method="POST" action="{{ route('book-requests.destroy', $bookRequest) }}" 
                                  onsubmit="return confirm('Are you sure you want to cancel this request?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">Cancel Request</button>
                            </form>
                        @endif
                        <a href="{{ route('book-requests.index') }}" class="btn btn-outline-secondary w-100">Back to Requests</a>
                    </div>
                @endif
            </div>
        </div>

        @if($bookRequest->status === 'approved' && $bookRequest->request_type === 'physical')
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Pickup Instructions</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Important:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Bring your Request ID: <code>{{ $bookRequest->request_id }}</code></li>
                            <li>Show this to library staff when picking up</li>
                            <li>You have 3 days to pick up the book</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
