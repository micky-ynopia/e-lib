@extends('layouts.app')

@section('title', 'Edit Book Request')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Book Request</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('book-requests.update', $bookRequest) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Request Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Request ID:</strong></td>
                                    <td><code>{{ $bookRequest->request_id }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Student:</strong></td>
                                    <td>{{ $bookRequest->user->name }} ({{ $bookRequest->user->student_id }})</td>
                                </tr>
                                <tr>
                                    <td><strong>Book:</strong></td>
                                    <td>{{ $bookRequest->book->title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Request Type:</strong></td>
                                    <td>
                                        <span class="badge {{ $bookRequest->request_type === 'physical' ? 'bg-primary' : 'bg-info' }}">
                                            {{ ucfirst($bookRequest->request_type) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="pending" {{ old('status', $bookRequest->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ old('status', $bookRequest->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('status', $bookRequest->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="fulfilled" {{ old('status', $bookRequest->status) === 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                                    <option value="cancelled" {{ old('status', $bookRequest->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="4" 
                                  placeholder="Add any notes about this request...">{{ old('notes', $bookRequest->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Request</button>
                        <a href="{{ route('book-requests.show', $bookRequest) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
