@extends('layouts.app')

@section('title', 'Submit Thesis')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Submit Thesis</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>Instructions:</strong> Please fill out all required fields and upload your thesis in PDF format (maximum 10MB).
                </div>

                <form method="POST" action="{{ route('theses.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Thesis Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="author_name" class="form-label">Author Name</label>
                            <input type="text" class="form-control @error('author_name') is-invalid @enderror" 
                                   id="author_name" name="author_name" value="{{ old('author_name', auth()->user()->name) }}" required>
                            @error('author_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="course" class="form-label">Course</label>
                            <select class="form-select @error('course') is-invalid @enderror" id="course" name="course" required>
                                <option value="">Select Course</option>
                                <option value="BSIT" {{ old('course', auth()->user()->course) == 'BSIT' ? 'selected' : '' }}>BS Information Technology</option>
                                <option value="BSCS" {{ old('course', auth()->user()->course) == 'BSCS' ? 'selected' : '' }}>BS Computer Science</option>
                                <option value="BSED" {{ old('course', auth()->user()->course) == 'BSED' ? 'selected' : '' }}>BS Education</option>
                                <option value="BEED" {{ old('course', auth()->user()->course) == 'BEED' ? 'selected' : '' }}>BE Elementary Education</option>
                                <option value="BSBA" {{ old('course', auth()->user()->course) == 'BSBA' ? 'selected' : '' }}>BS Business Administration</option>
                                <option value="BSA" {{ old('course', auth()->user()->course) == 'BSA' ? 'selected' : '' }}>BS Accountancy</option>
                                <option value="BSHM" {{ old('course', auth()->user()->course) == 'BSHM' ? 'selected' : '' }}>BS Hospitality Management</option>
                                <option value="BSA" {{ old('course', auth()->user()->course) == 'BSA' ? 'selected' : '' }}>BS Agriculture</option>
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
                                <option value="1st Year" {{ old('year_level', auth()->user()->year_level) == '1st Year' ? 'selected' : '' }}>1st Year</option>
                                <option value="2nd Year" {{ old('year_level', auth()->user()->year_level) == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                <option value="3rd Year" {{ old('year_level', auth()->user()->year_level) == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                <option value="4th Year" {{ old('year_level', auth()->user()->year_level) == '4th Year' ? 'selected' : '' }}>4th Year</option>
                            </select>
                            @error('year_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="academic_year" class="form-label">Academic Year</label>
                            <input type="text" class="form-control @error('academic_year') is-invalid @enderror" 
                                   id="academic_year" name="academic_year" value="{{ old('academic_year') }}" 
                                   placeholder="e.g., 2023-2024" required>
                            @error('academic_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="abstract" class="form-label">Abstract</label>
                        <textarea class="form-control @error('abstract') is-invalid @enderror" 
                                  id="abstract" name="abstract" rows="6" required>{{ old('abstract') }}</textarea>
                        @error('abstract')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="keywords" class="form-label">Keywords</label>
                        <input type="text" class="form-control @error('keywords') is-invalid @enderror" 
                               id="keywords" name="keywords" value="{{ old('keywords') }}" 
                               placeholder="Enter keywords separated by commas" required>
                        @error('keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="thesis_file" class="form-label">Thesis File (PDF)</label>
                        <input type="file" class="form-control @error('thesis_file') is-invalid @enderror" 
                               id="thesis_file" name="thesis_file" accept=".pdf" required>
                        <div class="form-text">Maximum file size: 10MB. Only PDF files are allowed.</div>
                        @error('thesis_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('theses.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit Thesis</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
