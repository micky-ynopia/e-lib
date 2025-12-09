@extends('layouts.app')

@section('title', 'New Book')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Add New Book</h1>
        <a href="{{ route('books.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Books
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
                    <h5 class="mb-0"><i class="bi bi-book"></i> Book Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="isbn" class="form-label">ISBN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('isbn') is-invalid @enderror" 
                                       id="isbn" name="isbn" value="{{ old('isbn') }}" required>
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
                                    <option value="">Select Author</option>
                                    @foreach ($authors as $author)
                                        <option value="{{ $author->id }}" 
                                                @selected(old('author_id') == $author->id)>
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
                                                @selected(old('category_id') == $category->id)>
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
                                       value="{{ old('published_year') }}" 
                                       min="0" max="9999">
                                @error('published_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="book_type" class="form-label">Book Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('book_type') is-invalid @enderror" 
                                        id="book_type" name="book_type" required onchange="toggleFileUpload()">
                                    <option value="">Select Type</option>
                                    <option value="physical" @selected(old('book_type') == 'physical')>Physical Only</option>
                                    <option value="digital" @selected(old('book_type') == 'digital')>Digital Only (E-Book)</option>
                                    <option value="both" @selected(old('book_type') == 'both')>Both Physical & Digital</option>
                                </select>
                                @error('book_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="total_copies" class="form-label">Total Copies <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('total_copies') is-invalid @enderror" 
                                       id="total_copies" name="total_copies" 
                                       value="{{ old('total_copies', 1) }}" 
                                       min="1" required>
                                @error('total_copies')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">For digital books, this is typically 1</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" 
                                      rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <!-- PDF Upload Section -->
                        <div id="pdfUploadSection" style="display: none;">
                            <h5 class="mb-3"><i class="bi bi-file-earmark-pdf"></i> PDF File Upload</h5>
                            
                            <div class="mb-3">
                                <label for="book_file" class="form-label">PDF File</label>
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

                        <!-- Cover Image Upload -->
                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Cover Image (Optional)</label>
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
                                    <option value="pending" @selected(old('status') == 'pending')>Pending</option>
                                    <option value="approved" @selected(old('status', 'approved') == 'approved')>Approved</option>
                                    <option value="rejected" @selected(old('status') == 'rejected')>Rejected</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                           value="1" @checked(old('is_featured'))>
                                    <label class="form-check-label" for="is_featured">
                                        <strong>Featured Book</strong>
                                        <small class="d-block text-muted">Show on homepage</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Book
                            </button>
                            <a href="{{ route('books.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Quick Guide</h6>
                </div>
                <div class="card-body">
                    <h6>Book Types:</h6>
                    <ul class="small">
                        <li><strong>Physical Only:</strong> Traditional printed books</li>
                        <li><strong>Digital Only:</strong> E-books (PDF files) - can be read online or downloaded</li>
                        <li><strong>Both:</strong> Available in both formats</li>
                    </ul>
                    
                    <h6 class="mt-3">PDF Requirements:</h6>
                    <ul class="small">
                        <li>Maximum size: 10MB</li>
                        <li>Format: PDF only</li>
                        <li>For digital books, PDF upload is required</li>
                    </ul>

                    <h6 class="mt-3">Features:</h6>
                    <ul class="small">
                        <li>✅ Read online in browser</li>
                        <li>✅ Download PDF files</li>
                        <li>✅ Track views & downloads</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFileUpload() {
    const bookType = document.getElementById('book_type').value;
    const pdfSection = document.getElementById('pdfUploadSection');
    
    if (bookType === 'digital' || bookType === 'both') {
        pdfSection.style.display = 'block';
        document.getElementById('book_file').required = true;
    } else {
        pdfSection.style.display = 'none';
        document.getElementById('book_file').required = false;
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
