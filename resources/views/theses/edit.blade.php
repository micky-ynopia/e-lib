@extends('layouts.app')

@section('title', 'Edit Thesis')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Thesis</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('theses.update', $thesis) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Thesis Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $thesis->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="author_name" class="form-label">Author Name</label>
                            <input type="text" class="form-control @error('author_name') is-invalid @enderror" 
                                   id="author_name" name="author_name" value="{{ old('author_name', $thesis->author_name) }}" required>
                            @error('author_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="course" class="form-label">Course</label>
                            <select class="form-select @error('course') is-invalid @enderror" id="course" name="course" required>
                                <option value="">Select Course</option>
                                <option value="BSIT" {{ old('course', $thesis->course) == 'BSIT' ? 'selected' : '' }}>BS Information Technology</option>
                                <option value="BSCS" {{ old('course', $thesis->course) == 'BSCS' ? 'selected' : '' }}>BS Computer Science</option>
                                <option value="BSED" {{ old('course', $thesis->course) == 'BSED' ? 'selected' : '' }}>BS Education</option>
                                <option value="BEED" {{ old('course', $thesis->course) == 'BEED' ? 'selected' : '' }}>BE Elementary Education</option>
                                <option value="BSBA" {{ old('course', $thesis->course) == 'BSBA' ? 'selected' : '' }}>BS Business Administration</option>
                                <option value="BSA" {{ old('course', $thesis->course) == 'BSA' ? 'selected' : '' }}>BS Accountancy</option>
                                <option value="BSHM" {{ old('course', $thesis->course) == 'BSHM' ? 'selected' : '' }}>BS Hospitality Management</option>
                                <option value="BSA" {{ old('course', $thesis->course) == 'BSA' ? 'selected' : '' }}>BS Agriculture</option>
                            </select>
                            @error('course')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="year_level" class="form-label">Year Level</label>
                            <select class="form-select @error('year_level') is-invalid @enderror" id="year_level" name="year_level" required>
                                <option value="">Select Year</option>
                                <option value="1st Year" {{ old('year_level', $thesis->year_level) == '1st Year' ? 'selected' : '' }}>1st Year</option>
                                <option value="2nd Year" {{ old('year_level', $thesis->year_level) == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                <option value="3rd Year" {{ old('year_level', $thesis->year_level) == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                <option value="4th Year" {{ old('year_level', $thesis->year_level) == '4th Year' ? 'selected' : '' }}>4th Year</option>
                            </select>
                            @error('year_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="academic_year" class="form-label">Academic Year</label>
                            <input type="text" class="form-control @error('academic_year') is-invalid @enderror" 
                                   id="academic_year" name="academic_year" value="{{ old('academic_year', $thesis->academic_year) }}" 
                                   placeholder="e.g., 2023-2024" required>
                            @error('academic_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="abstract" class="form-label">Abstract</label>
                        <textarea class="form-control @error('abstract') is-invalid @enderror" 
                                  id="abstract" name="abstract" rows="6" required>{{ old('abstract', $thesis->abstract) }}</textarea>
                        @error('abstract')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="keywords" class="form-label">Keywords</label>
                        <input type="text" class="form-control @error('keywords') is-invalid @enderror" 
                               id="keywords" name="keywords" value="{{ old('keywords', $thesis->keywords) }}" 
                               placeholder="Enter keywords separated by commas" required>
                        @error('keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="pending" {{ old('status', $thesis->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status', $thesis->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ old('status', $thesis->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason (if rejected)</label>
                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror" 
                                  id="rejection_reason" name="rejection_reason" rows="3">{{ old('rejection_reason', $thesis->rejection_reason) }}</textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('theses.show', $thesis) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Thesis</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
