@extends('layouts.app')

@section('title', 'Thesis Repository')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 m-0">Thesis Repository</h1>
            @auth
                @if(auth()->user()->isStudent())
                    <a href="{{ route('theses.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Submit Thesis
                    </a>
                @endif
            @endauth
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('theses.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Search by title, author, or keywords..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="course">
                    <option value="">All Courses</option>
                    <option value="BSIT" {{ request('course') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                    <option value="BSCS" {{ request('course') == 'BSCS' ? 'selected' : '' }}>BSCS</option>
                    <option value="BSED" {{ request('course') == 'BSED' ? 'selected' : '' }}>BSED</option>
                    <option value="BEED" {{ request('course') == 'BEED' ? 'selected' : '' }}>BEED</option>
                    <option value="BSBA" {{ request('course') == 'BSBA' ? 'selected' : '' }}>BSBA</option>
                    <option value="BSA" {{ request('course') == 'BSA' ? 'selected' : '' }}>BSA</option>
                    <option value="BSHM" {{ request('course') == 'BSHM' ? 'selected' : '' }}>BSHM</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
            @if(request()->hasAny(['search', 'course']))
                <div class="col-md-2">
                    <a href="{{ route('theses.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

@forelse($theses as $thesis)
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-1">{{ $thesis->title }}</h5>
                <small class="text-muted">
                    {{ $thesis->author_name }} • {{ $thesis->course }} • {{ $thesis->academic_year }}
                </small>
            </div>
            <div>
                <span class="badge {{ $thesis->status_badge_class }}">
                    {{ ucfirst($thesis->status) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <p class="card-text">{{ Str::limit($thesis->abstract, 300) }}</p>
            
            <div class="row">
                <div class="col-md-6">
                    <strong>Keywords:</strong> {{ $thesis->keywords }}
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        File Size: {{ $thesis->formatted_file_size }}
                    </small>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('theses.show', $thesis) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye"></i> View Details
                </a>
                @if($thesis->status === 'approved')
                    <a href="{{ route('theses.download', $thesis) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-download"></i> Download
                    </a>
                @endif
                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('theses.edit', $thesis) }}" class="btn btn-outline-secondary">
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
@empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-file-text" style="font-size: 3rem; color: #6c757d;"></i>
            <h5 class="mt-3">No Theses Found</h5>
            <p class="text-muted">There are no theses to display at this time.</p>
            @auth
                @if(auth()->user()->isStudent())
                    <a href="{{ route('theses.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle"></i> Submit Your First Thesis
                    </a>
                @endif
            @endauth
        </div>
    </div>
@endforelse

<div class="d-flex justify-content-center">
    {{ $theses->links() }}
</div>
@endsection
