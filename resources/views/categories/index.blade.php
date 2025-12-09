@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Categories</h1>
    @auth
    @if(auth()->user()->isAdmin())
    <a href="{{ route('categories.create') }}" class="btn btn-success">New Category</a>
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
        @forelse ($categories as $category)
            <tr>
                <td>
                    <a href="{{ route('categories.show', $category) }}" class="text-decoration-none">{{ $category->name }}</a>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        @auth
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                        @endif
                        @endauth
                        <a href="{{ route('categories.show', $category) }}" class="btn btn-sm btn-outline-secondary">View</a>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="2" class="text-muted">No categories yet.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">{{ $categories->links() }}</div>
@endsection


