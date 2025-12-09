@extends('layouts.app')

@section('title', 'Reading: ' . $book->title)

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">
                <i class="bi bi-book"></i> {{ $book->title }}
            </h5>
            <small>by {{ $book->author->first_name }} {{ $book->author->last_name }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-light" title="Back to book details">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            @if($book->isDigital())
            <a href="{{ route('books.download', $book) }}" class="btn btn-sm btn-success" title="Download">
                <i class="bi bi-download"></i> Download
            </a>
            @endif
        </div>
    </div>
    <div class="card-body p-0" style="min-height: calc(100vh - 250px);">
        @php
            // Get the correct file path
            $filePath = $book->file_path;
            // Generate the URL using Storage facade
            $pdfUrl = $filePath ? Storage::disk('public')->url($filePath) : '#';
        @endphp
        @if($filePath && Storage::disk('public')->exists($filePath))
        <iframe 
            src="{{ $pdfUrl }}#toolbar=0" 
            style="width: 100%; height: calc(100vh - 250px); border: none;"
            title="PDF Viewer for {{ $book->title }}"
            type="application/pdf">
            <p>Your browser does not support PDFs. 
                <a href="{{ route('books.download', $book) }}">Download the PDF instead</a>.
            </p>
        </iframe>
        @else
        <div class="alert alert-warning m-3">
            <i class="bi bi-exclamation-triangle"></i> PDF file not found. Please contact the administrator.
        </div>
        @endif
    </div>
    <div class="card-footer bg-light">
        <div class="row text-muted small">
            <div class="col-md-4">
                <strong><i class="bi bi-eye"></i> Views:</strong> {{ number_format($book->view_count) }}
            </div>
            <div class="col-md-4">
                <strong><i class="bi bi-download"></i> Downloads:</strong> {{ number_format($book->download_count) }}
            </div>
            <div class="col-md-4">
                <strong><i class="bi bi-file-earmark"></i> Size:</strong> {{ $book->formatted_file_size }}
            </div>
        </div>
    </div>
</div>
@endsection

