@extends('layouts.app')

@section('title', 'Edit Book')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Edit Book</h1>
        <a href="{{ route('books.show', $book) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Book
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Book Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('books.update', $book) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $book->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="isbn" class="form-label">ISBN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('isbn') is-invalid @enderror" 
                                       id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}" required>
                                @error('isbn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="author_id" class="form-label">Author <span class="text-danger">*</span></label>
                                <select class="form-select @error('author_id') is-invalid @enderror" 
                                        id="author_id" name="author_id" required>
                                    @foreach ($authors as $author)
                                        <option value="{{ $author->id }}" 
                                                @selected(old('author_id', $book->author_id) == $author->id)>
                                            {{ $author->last_name }}, {{ $author->first_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('author_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id">
                                    <option value="">None</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                @selected(old('category_id', $book->category_id) == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="published_year" class="form-label">Published Year</label>
                                <input type="number" class="form-control @error('published_year') is-invalid @enderror" 
                                       id="published_year" name="published_year" 
                                       value="{{ old('published_year', $book->published_year) }}" 
                                       min="0" max="9999">
                                @error('published_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="book_type" class="form-label">Book Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('book_type') is-invalid @enderror" 
                                        id="book_type" name="book_type" required onchange="toggleFileUpload()">
                                    <option value="physical" @selected(old('book_type', $book->book_type) == 'physical')>Physical Only</option>
                                    <option value="digital" @selected(old('book_type', $book->book_type) == 'digital')>Digital Only (E-Book)</option>
                                    <option value="both" @selected(old('book_type', $book->book_type) == 'both')>Both Physical & Digital</option>
                                </select>
                                @error('book_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="total_copies" class="form-label">Total Copies <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('total_copies') is-invalid @enderror" 
                                       id="total_copies" name="total_copies" 
                                       value="{{ old('total_copies', $book->total_copies) }}" 
                                       min="1" required>
                                @error('total_copies')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" 
                                      rows="4">{{ old('description', $book->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <!-- Current PDF File Info -->
                        @if($book->file_path)
                        <div class="alert alert-info">
                            <strong><i class="bi bi-file-earmark-pdf"></i> Current PDF File:</strong>
                            <div class="mt-2">
                                <p class="mb-1"><strong>Filename:</strong> {{ $book->file_name }}</p>
                                <p class="mb-1"><strong>Size:</strong> {{ $book->formatted_file_size }}</p>
                                @if($book->isDigital())
                                <div class="mt-2">
                                    <a href="{{ route('books.read', $book) }}" class="btn btn-sm btn-primary" target="_blank">
                                        <i class="bi bi-eye"></i> View PDF
                                    </a>
                                    <a href="{{ route('books.download', $book) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- PDF Upload Section -->
                        <div id="pdfUploadSection" style="display: none;">
                            <h5 class="mb-3"><i class="bi bi-file-earmark-pdf"></i> PDF File Upload</h5>
                            
                            <div class="mb-3">
                                <label for="book_file" class="form-label">
                                    @if($book->file_path)
                                        Replace PDF File (leave empty to keep current)
                                    @else
                                        Upload PDF File
                                    @endif
                                </label>
                                <input type="file" class="form-control @error('book_file') is-invalid @enderror" 
                                       id="book_file" name="book_file" 
                                       accept=".pdf" 
                                       onchange="checkFileSize(this)">
                                @error('book_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="bi bi-info-circle"></i> Maximum file size: 10MB. Only PDF files are accepted.
                                </small>
                                <div id="fileSizeWarning" class="text-danger small mt-1" style="display: none;"></div>
                            </div>
                        </div>

                        <!-- Current Cover Image -->
                        @if($book->cover_image)
                        <div class="mb-3">
                            <label class="form-label">Current Cover Image</label>
                            <div>
                                <img src="{{ Storage::disk('public')->url($book->cover_image) }}" 
                                     alt="Cover" 
                                     style="max-width: 200px; max-height: 300px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                        </div>
                        @endif

                        <!-- Cover Image Upload -->
                        <div class="mb-3">
                            <label for="cover_image" class="form-label">
                                @if($book->cover_image)
                                    Replace Cover Image (leave empty to keep current)
                                @else
                                    Cover Image (Optional)
                                @endif
                            </label>
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" 
                                   id="cover_image" name="cover_image" 
                                   accept="image/*">
                            @error('cover_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="bi bi-info-circle"></i> Maximum file size: 2MB. Formats: JPEG, PNG, JPG, GIF
                            </small>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="pending" @selected(old('status', $book->status) == 'pending')>Pending</option>
                                    <option value="approved" @selected(old('status', $book->status) == 'approved')>Approved</option>
                                    <option value="rejected" @selected(old('status', $book->status) == 'rejected')>Rejected</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                           value="1" @checked(old('is_featured', $book->is_featured))>
                                    <label class="form-check-label" for="is_featured">
                                        <strong>Featured Book</strong>
                                        <small class="d-block text-muted">Show on homepage</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Book
                            </button>
                            <a href="{{ route('books.show', $book) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Book Statistics</h6>
                </div>
                <div class="card-body">
                    <p><strong>Views:</strong> {{ number_format($book->view_count ?? 0) }}</p>
                    <p><strong>Downloads:</strong> {{ number_format($book->download_count ?? 0) }}</p>
                    @if($book->file_path)
                    <p><strong>File Size:</strong> {{ $book->formatted_file_size }}</p>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Important</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Uploading a new PDF will replace the existing one</li>
                        <li>Old files are automatically deleted</li>
                        <li>Digital books require a PDF file</li>
                        <li>Changes take effect immediately</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    use Illuminate\Support\Facades\Storage;
@endphp

<script>
function toggleFileUpload() {
    const bookType = document.getElementById('book_type').value;
    const pdfSection = document.getElementById('pdfUploadSection');
    
    if (bookType === 'digital' || bookType === 'both') {
        pdfSection.style.display = 'block';
    } else {
        pdfSection.style.display = 'none';
    }
}

function checkFileSize(input) {
    const file = input.files[0];
    const warningDiv = document.getElementById('fileSizeWarning');
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    
    if (file) {
        if (file.size > maxSize) {
            warningDiv.textContent = `File size (${(file.size / 1024 / 1024).toFixed(2)}MB) exceeds 10MB limit!`;
            warningDiv.style.display = 'block';
            input.value = '';
        } else {
            warningDiv.style.display = 'none';
        }
        
        if (!file.type.includes('pdf')) {
            warningDiv.textContent = 'Please select a PDF file!';
            warningDiv.style.display = 'block';
            input.value = '';
        }
    }
}

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleFileUpload();
});
</script>
@endsection
