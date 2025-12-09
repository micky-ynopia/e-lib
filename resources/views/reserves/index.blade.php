@extends('layouts.app')

@section('title', 'My Reservations')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 m-0">
        <i class="bi bi-calendar-check"></i> 
        {{ auth()->user()->isAdmin() ? 'Book Reservations' : 'My Reservations' }}
    </h1>
    @if(!auth()->user()->isAdmin())
    <a href="{{ route('reserves.create')bridgedIIeList class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Reservation
    </a>
    @endif
</div>

@forelse($reserves as $reserve)
<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h5 class="mb-2">
                    <a href="{{ route('books.show', $reserve->book) }}" class="text-decoration-none">
                        {{ $reserve->book->title }}
                    </a>
                </h5>
                <p class="text-muted mb-1">
                    <i class="bi bi-person"></i> {{ $reserve->book->author->first_name }} {{ $reserve->book->author->last_name }}
                </p>
                <p class="text-muted mb-2">
                    <i class="bi bi-calendar"></i> Reserved on: {{ $reserve->reserved_at->format('M d, Y h:i A') }}
                </p>
                @if($reserve->status === 'available')
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle"></i> <strong>Available!</strong> 
                    @if($reserve->expires_at > now())
                        Expires: {{ $reserve->expires_at->format('M d, Y') }}
                    @else
                        <strong class="text-danger">Expired</strong>
                    @endif
                </div>
                @endif
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-{{ 
                    $reserve->status === 'available' ? 'success' : 
                    ($reserve->status === 'fulfilled' ? 'primary' : 
                    ($reserve->status === 'cancelled' ? 'secondary' : 'warning'))
                }} fs-6 mb-2">
                    {{ ucfirst($reserve->status) }}
                </span>
                
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('books.show', $reserve->book) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> View Book
                    </a>
                    
                    @if(auth()->user()->isAdmin())
                        @if($reserve->status === 'available')
                        <form method="POST" action="{{ route('reserves.fulfill', $reserve) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="bi bi-check"></i> Mark Fulfilled
                            </button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('reserves.update', $reserve) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-circle"></i> Cancel
                            </button>
                        </form>
                    @else
                        @if($reserve->status === 'pending')
                        <form method="POST" action="{{ route('reserves.destroy', $reserve) }}" onsubmit="return confirm('Cancel this reservation?')" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-x-circle"></i> Cancel
                            </button>
                        </form>
                        @endif
                    @endif
                </div>
                
                @if(auth()->user()->isAdmin() && $reserve->user)
                <p class="text-muted small mt-2 mb-0">
                    Reserved by: <strong>{{ $reserve->user->name }}</strong>
                </p>
                @endif
            </div>
        </div>
        @if($reserve->notes)
        <div class="mt-2">
            <small class="text-muted"><i class="bi bi-chat-left-text"></i> Note: {{ $reserve->notes }}</small>
        </div>
        @endif
    </div>
</div>
@empty
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> No reservations found.
    @if(!auth()->user()->isAdmin())
    <a href="{{ route('reserves.create') }}" class="alert-link">Create your first reservation</a>
    @endif
</div>
@endforelse

<div class="mt-3">
    {{ $reserves->links() }}
</div>
@endsection

