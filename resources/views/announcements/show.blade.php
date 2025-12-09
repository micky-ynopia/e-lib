@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">{{ $announcement->title }}</h5>
                    <small class="text-muted">
                        {{ $announcement->creator->name }} • {{ $announcement->created_at->format('M d, Y \a\t g:i A') }}
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
                <div class="announcement-content">
                    {!! nl2br(e($announcement->content)) !!}
                </div>
                
                @if($announcement->expires_at)
                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-muted">
                            <i class="bi bi-clock"></i> 
                            This announcement expires on {{ $announcement->expires_at->format('M d, Y \a\t g:i A') }}
                        </small>
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('announcements.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Announcements
                    </a>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <div class="btn-group">
                                <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-outline-primary">
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
    </div>
</div>
@endsection
