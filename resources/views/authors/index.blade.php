@extends('layouts.app')

@section('title', 'Authors')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Authors</h1>
    @auth
    @if(auth()->user()->isAdmin())
    <a href="{{ route('authors.create') }}" class="btn btn-success">New Author</a>
    @endif
    @endauth
    </div>

<table class="table table-hover align-middle">
    <thead>
        <tr>
            <th>Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($authors as $author)
            <tr>
                <td>
                    <a href="{{ route('authors.show', $author) }}" class="text-decoration-none">{{ $author->last_name }}, {{ $author->first_name }}</a>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        @auth
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('authors.edit', $author) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('authors.destroy', $author) }}" onsubmit="return confirm('Delete this author?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                        @endif
                        @endauth
                        <a href="{{ route('authors.show', $author) }}" class="btn btn-sm btn-outline-secondary">View</a>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="2" class="text-muted">No authors yet.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">{{ $authors->links() }}</div>
@endsection


