@extends('layouts.app')

@section('title', $thesis->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">{{ $thesis->title }}</h5>
                    <small class="text-muted">
                        Submitted on {{ $thesis->created_at->format('M d, Y \a\t g:i A') }}
                    </small>
                </div>
                <span class="badge {{ $thesis->status_badge_class }}">
                    {{ ucfirst($thesis->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="fw-bold">Abstract</h6>
                        <p class="mb-4">{{ $thesis->abstract }}</p>
                        
                        <h6 class="fw-bold">Keywords</h6>
                        <p class="mb-4">{{ $thesis->keywords }}</p>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Thesis Information</h6>
                                <ul class="list-unstyled mb-0">
                                    <li><strong>Author:</strong> {{ $thesis->author_name }}</li>
                                    <li><strong>Course:</strong> {{ $thesis->course }}</li>
                                    <li><strong>Year Level:</strong> {{ $thesis->year_level }}</li>
                                    <li><strong>Academic Year:</strong> {{ $thesis->academic_year }}</li>
                                    <li><strong>File Size:</strong> {{ $thesis->formatted_file_size }}</li>
                                    @if($thesis->approved_by)
                                        <li><strong>Approved by:</strong> {{ $thesis->approver->name }}</li>
                                        <li><strong>Approved on:</strong> {{ $thesis->approved_at->format('M d, Y') }}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        
                        @if($thesis->status === 'approved')
                            <div class="mt-3">
                                <a href="{{ route('theses.download', $thesis) }}" class="btn btn-success w-100">
                                    <i class="bi bi-download"></i> Download Thesis
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                @if($thesis->status === 'rejected' && $thesis->rejection_reason)
                    <div class="alert alert-danger mt-4">
                        <h6 class="alert-heading">Rejection Reason</h6>
                        <p class="mb-0">{{ $thesis->rejection_reason }}</p>
                    </div>
                @endif

                <!-- Citations -->
                <div class="mt-4">
                    <h6 class="fw-bold">Citations</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">APA Format:</h6>
                            <p class="small bg-light p-2 rounded">{{ $thesis->apa_citation }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">MLA Format:</h6>
                            <p class="small bg-light p-2 rounded">{{ $thesis->mla_citation }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('theses.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Repository
                    </a>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <div class="btn-group">
                                <a href="{{ route('theses.edit', $thesis) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('theses.destroy', $thesis) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
