@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Create New User</h1>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card">
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

        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" 
                           id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" 
                            id="role" name="role" required onchange="toggleStudentFields()">
                        <option value="">Select Role</option>
                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="librarian" {{ old('role') == 'librarian' ? 'selected' : '' }}>Librarian</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="text" class="form-control @error('student_id') is-invalid @enderror" 
                           id="student_id" name="student_id" value="{{ old('student_id') }}">
                    @error('student_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Required for students only</small>
                </div>
            </div>

            <div class="row" id="studentFields">
                <div class="col-md-6 mb-3">
                    <label for="course" class="form-label">Course</label>
                    <input type="text" class="form-control @error('course') is-invalid @enderror" 
                           id="course" name="course" value="{{ old('course') }}" 
                           placeholder="e.g., BSIT, BSCS">
                    @error('course')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="year_level" class="form-label">Year Level</label>
                    <input type="text" class="form-control @error('year_level') is-invalid @enderror" 
                           id="year_level" name="year_level" value="{{ old('year_level') }}" 
                           placeholder="e.g., 1st Year, 2nd Year">
                    @error('year_level')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone') }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="is_approved" class="form-label">Account Status</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" 
                               value="1" {{ old('is_approved', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_approved">
                            Approved (user can login immediately)
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleStudentFields() {
    const role = document.getElementById('role').value;
    const studentFields = document.getElementById('studentFields');
    const studentId = document.getElementById('student_id');
    
    if (role === 'student') {
        studentFields.style.display = '';
        studentId.required = true;
    } else {
        studentFields.style.display = 'none';
        studentId.required = false;
        document.getElementById('course').value = '';
        document.getElementById('year_level').value = '';
    }
}

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleStudentFields();
});
</script>
@endsection

