@extends('layouts.app')

@section('title', 'Books')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Books</h1>
    @auth
    @if(auth()->user()->isAdmin())
    <a href="{{ route('books.create') }}" class="btn btn-success"><i class="bi bi-plus-circle"></i> New Book</a>
    @endif
    @endauth
</div>

<!-- Search and Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('books.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Title, author, ISBN...">
            </div>
            <div class="col-md-2">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All Categories</option>
                    @foreach(\App\Models\Category::all() as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="book_type" class="form-label">Type</label>
                <select class="form-select" id="book_type" name="book_type">
                    <option value="">All Types</option>
                    <option value="physical" {{ request('book_type') == 'physical' ? 'selected' : '' }}>Physical</option>
                    <option value="digital" {{ request('book_type') == 'digital' ? 'selected' : '' }}>Digital</option>
                    <option value="both" {{ request('book_type') == 'both' ? 'selected' : '' }}>Both</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Operations (Admin Only) -->
@auth
@if(auth()->user()->isAdmin())
<form id="bulkForm" method="POST">
    @csrf
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll()">Select All</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">Deselect All</button>
        </div>
        <div>
            <button type="submit" formaction="{{ route('books.bulk-approve') }}" class="btn btn-sm btn-success" onclick="return confirmBulk('approve')">
                <i class="bi bi-check-circle"></i> Bulk Approve
            </button>
            <button type="submit" formaction="{{ route('books.bulk-reject') }}" class="btn btn-sm btn-warning" onclick="return confirmBulk('reject')">
                <i class="bi bi-x-circle"></i> Bulk Reject
            </button>
            <button type="submit" formaction="{{ route('books.bulk-delete') }}" class="btn btn-sm btn-danger" onclick="return confirmBulk('delete')">
                <i class="bi bi-trash"></i> Bulk Delete
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="40"><input type="checkbox" id="selectAllCheck" onchange="toggleSelectAll()"></th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($books as $book)
                    <tr>
                        <td><input type="checkbox" name="book_ids[]" value="{{ $book->id }}" class="row-checkbox"></td>
                        <td>
                            <a href="{{ route('books.show', $book) }}" class="text-decoration-none">{{ $book->title }}</a>
                            @if($book->is_featured ?? false)
                                <span class="badge bg-warning">Featured</span>
                            @endif
                        </td>
                        <td>{{ $book->author?->last_name }}, {{ $book->author?->first_name }}</td>
                        <td>{{ $book->category?->name ?? '—' }}</td>
                        <td><span class="badge bg-info">{{ ucfirst($book->book_type ?? 'physical') }}</span></td>
                        <td>
                            <span class="badge bg-{{ $book->available_copies > 0 ? 'success' : 'danger' }}">
                                {{ $book->available_copies }} / {{ $book->total_copies }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ ($book->status ?? 'approved') == 'approved' ? 'success' : (($book->status ?? 'approved') == 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($book->status ?? 'approved') }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('Delete this book?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-muted text-center py-4">No books found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>
@else
<!-- Non-Admin View -->
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Type</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($books as $book)
                <tr>
                    <td>
                        <a href="{{ route('books.show', $book) }}" class="text-decoration-none">{{ $book->title }}</a>
                        @if($book->is_featured ?? false)
                            <span class="badge bg-warning">Featured</span>
                        @endif
                    </td>
                    <td>{{ $book->author?->last_name }}, {{ $book->author?->first_name }}</td>
                    <td>{{ $book->category?->name ?? '—' }}</td>
                    <td><span class="badge bg-info">{{ ucfirst($book->book_type ?? 'physical') }}</span></td>
                    <td>
                        <span class="badge bg-{{ $book->available_copies > 0 ? 'success' : 'danger' }}">
                            {{ $book->available_copies }} / {{ $book->total_copies }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-secondary" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($book->isDigital() && auth()->check())
                            <a href="{{ route('books.read', $book) }}" class="btn btn-sm btn-outline-primary" title="Read Online">
                                <i class="bi bi-book"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted text-center py-4">No books found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif
@endauth

<div class="mt-3">{{ $books->links() }}</div>

@auth
@if(auth()->user()->isAdmin())
<script>
function selectAll() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = true);
    document.getElementById('selectAllCheck').checked = true;
}

function deselectAll() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAllCheck').checked = false;
}

function toggleSelectAll() {
    const checked = document.getElementById('selectAllCheck').checked;
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = checked);
}

function confirmBulk(action) {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    if (checked.length === 0) {
        alert('Please select at least one book.');
        return false;
    }
    return confirm(`Are you sure you want to ${action} ${checked.length} book(s)?`);
}
</script>
@endif
@endauth
@endsection
