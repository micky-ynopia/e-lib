@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 m-0">Announcements</h1>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('announcements.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> New Announcement
                    </a>
                @endif
            @endauth
        </div>
    </div>
</div>

@forelse($announcements as $announcement)
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-1">{{ $announcement->title }}</h5>
                <small class="text-muted">
                    {{ $announcement->creator->name }} • {{ $announcement->created_at->format('M d, Y') }}
                </small>
            </div>
            <div>
                <span class="badge {{ $announcement->type_badge_class }} me-1">
                    {{ ucfirst($announcement->type) }}
                </span>
                <span class="badge {{ $announcement->priority_badge_class }}">
                    {{ ucfirst($announcement->priority) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <p class="card-text">{{ Str::limit($announcement->content, 200) }}</p>
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-outline-primary btn-sm">
                    Read More
                </a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" class="d-inline">
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
            <i class="bi bi-megaphone" style="font-size: 3rem; color: #6c757d;"></i>
            <h5 class="mt-3">No Announcements</h5>
            <p class="text-muted">There are no announcements to display at this time.</p>
        </div>
    </div>
@endforelse

<div class="d-flex justify-content-center">
    {{ $announcements->links() }}
</div>
@endsection
