@extends('layouts.app')

@section('title', 'Reserve a Book')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar-plus"></i> Reserve a Physical Book</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('reserves.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="book_id" class="form-label">Select Book <span class="text-danger">*</span></label>
                        <select class="form-select @error('book_id') is-invalid @enderror" id="book_id" name="book_id" required>
                            <option value="">-- Select a book --</option>
                            @foreach($books as $book)
                            <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                {{ $book->title }} - {{ $book->author->first_name }} {{ $book->author->last_name }}
                                @if($book->available_copies > 0)
                                    (Available: {{ $book->available_copies }})
                                @else
                                    (Currently Unavailable)
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @error('book_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Select a physical book to reserve</small>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="3" 
                                  placeholder="Add any special requests or notes...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Max 500 characters</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Reservation Policy:</strong>
                        <ul class="mb-0 mt-2">
                            <li>If the book is available, you will be notified immediately</li>
                            <li>If unavailable, you will be notified when it becomes available</li>
                            <li>Reserved books must be picked up within 7 days</li>
                            <li>You can only have one pending reservation per book</li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Reserve Book
                        </button>
                        <a href="{{ route('reserves.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

