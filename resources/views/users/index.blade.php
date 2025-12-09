@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">User Management</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New User
    </a>
</div>

<!-- Search and Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('users.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name, email, student ID, course...">
            </div>
            <div class="col-md-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role">
                    <option value="">All Roles</option>
                    <option value="librarian" {{ request('role') == 'librarian' ? 'selected' : '' }}>Librarian</option>
                    <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="approval_status" class="form-label">Approval Status</label>
                <select class="form-select" id="approval_status" name="approval_status">
                    <option value="">All</option>
                    <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form id="bulkForm" method="POST">
    @csrf
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll()">Select All</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">Deselect All</button>
        </div>
        <div>
            <button type="submit" formaction="{{ route('users.bulk-approve') }}" class="btn btn-sm btn-success" onclick="return confirmBulk('approve')">
                <i class="bi bi-check-circle"></i> Bulk Approve
            </button>
            <button type="submit" formaction="{{ route('users.bulk-reject') }}" class="btn btn-sm btn-warning" onclick="return confirmBulk('reject')">
                <i class="bi bi-x-circle"></i> Bulk Reject
            </button>
            <button type="submit" formaction="{{ route('users.bulk-delete') }}" class="btn btn-sm btn-danger" onclick="return confirmBulk('delete')">
                <i class="bi bi-trash"></i> Bulk Delete
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="40"><input type="checkbox" id="selectAllCheck" onchange="toggleSelectAll()"></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Student ID</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="row-checkbox">
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-info">{{ $user->role_display }}</span></td>
                        <td>{{ $user->student_id ?? '—' }}</td>
                        <td>{{ $user->course ?? '—' }}</td>
                        <td>
                            @if($user->is_approved)
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(!$user->is_approved)
                                    <form method="POST" action="{{ route('users.approve', $user) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('users.reject', $user) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning" title="Reject">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>

<div class="mt-3">{{ $users->links() }}</div>

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
        alert('Please select at least one user.');
        return false;
    }
    return confirm(`Are you sure you want to ${action} ${checked.length} user(s)?`);
}
</script>
@endsection

