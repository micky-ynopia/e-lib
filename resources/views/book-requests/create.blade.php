@extends('layouts.app')

@section('title', 'Request Book')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Request a Book</h5>
            </div>
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

                <form method="POST" action="{{ route('book-requests.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="book_id" class="form-label">Select Book</label>
                        <select class="form-select @error('book_id') is-invalid @enderror" 
                                id="book_id" name="book_id" required>
                            <option value="">Choose a book...</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                    {{ $book->title }} - {{ $book->author->first_name }} {{ $book->author->last_name }}
                                    ({{ $book->available_copies }} available)
                                </option>
                            @endforeach
                        </select>
                        @error('book_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Request Type</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="request_type" 
                                           id="physical" value="physical" 
                                           {{ old('request_type', 'physical') === 'physical' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="physical">
                                        <strong>Physical Book</strong>
                                        <br>
                                        <small class="text-muted">Pick up from library</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="request_type" 
                                           id="digital" value="digital" 
                                           {{ old('request_type') === 'digital' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="digital">
                                        <strong>Digital Book</strong>
                                        <br>
                                        <small class="text-muted">Download e-book</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('request_type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes (Optional)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Any special requests or notes...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Important:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Your request will be reviewed by library staff</li>
                            <li>You'll receive a Request ID that serves as your Borrow Slip</li>
                            <li>For physical books, bring your Request ID when picking up</li>
                            <li>You'll receive email notifications about your request status</li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                        <a href="{{ route('book-requests.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookSelect = document.getElementById('book_id');
    const physicalRadio = document.getElementById('physical');
    const digitalRadio = document.getElementById('digital');
    
    function updateBookOptions() {
        const selectedBookId = bookSelect.value;
        const selectedOption = bookSelect.options[bookSelect.selectedIndex];
        
        if (selectedOption.text.includes('(0 available)')) {
            physicalRadio.disabled = true;
            digitalRadio.checked = true;
        } else {
            physicalRadio.disabled = false;
        }
    }
    
    bookSelect.addEventListener('change', updateBookOptions);
    updateBookOptions();
});
</script>
@endsection
