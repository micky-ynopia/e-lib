@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp
<style>
    .hero {
        position: relative;
        border-radius: .5rem;
        background: linear-gradient(rgba(13,110,253,.85), rgba(13,110,253,.85)), url('{{ asset('images/hero-library.jpg') }}') center/cover no-repeat;
        color: #fff;
        overflow: hidden;
    }
    .hero .content { padding: 5rem 2rem; }
    .feature-icon { width: 42px; height: 42px; border-radius: .5rem; display:flex; align-items:center; justify-content:center; }
</style>

<section class="hero shadow-sm mb-4">
    <div class="content text-center">
        <div class="d-flex justify-content-center mb-3">
            <img src="{{ asset('images/nemsu-logo.jpg') }}" alt="NEMSU" style="width:70px;height:70px;object-fit:contain;filter: drop-shadow(0 2px 6px rgba(0,0,0,.2));" />
        </div>
        <h1 class="display-5 fw-bold mb-3">E-Library Management System</h1>
        <p class="lead mb-4" style="max-width: 780px; margin: 0 auto;">
            A modern, blue-themed portal to explore digital and physical collections, request borrows, and track due dates—built for NEMSU Cantilan.
        </p>
        <div class="d-flex justify-content-center gap-2 mt-2">
            @guest
                <a href="{{ route('login') }}" class="btn btn-light btn-lg">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">Create Account</a>
            @else
                @if(auth()->user()->isLibrarian())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-lg">Go to Dashboard</a>
                @elseif(auth()->user()->isStaff())
                    <a href="{{ route('staff.dashboard') }}" class="btn btn-light btn-lg">Go to Dashboard</a>
                @else
                    <a href="{{ route('student.dashboard') }}" class="btn btn-light btn-lg">My Dashboard</a>
                @endif
            @endguest
            <a href="{{ route('books.index') }}" class="btn btn-outline-light btn-lg">Browse Collection</a>
        </div>
    </div>
</section>

@if(isset($stats))
<section class="mb-4">
    <div class="row g-3 text-center">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-primary mb-0">{{ number_format($stats['total_books']) }}</h3>
                    <small class="text-muted">Total Books</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-primary mb-0">{{ number_format($stats['digital_books']) }}</h3>
                    <small class="text-muted">Digital Books</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-primary mb-0">{{ number_format($stats['total_categories']) }}</h3>
                    <small class="text-muted">Categories</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-primary mb-0">{{ number_format($stats['total_authors']) }}</h3>
                    <small class="text-muted">Authors</small>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@if(isset($featuredBooks) && $featuredBooks->count() > 0)
<section class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Featured Books</h2>
        <a href="{{ route('books.index', ['featured' => 1]) }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="row g-3">
        @foreach($featuredBooks as $book)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm">
                @if($book->cover_image)
                <img src="{{ Storage::url($book->cover_image) }}" class="card-img-top" alt="{{ $book->title }}" style="height: 200px; object-fit: cover;">
                @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                </div>
                @endif
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title mb-2">{{ Str::limit($book->title, 50) }}</h6>
                    <p class="text-muted small mb-2">
                        <strong>{{ $book->author?->last_name }}, {{ $book->author?->first_name }}</strong>
                    </p>
                    <p class="text-muted small mb-2">
                        <span class="badge bg-secondary">{{ $book->category?->name ?? 'Uncategorized' }}</span>
                    </p>
                    <div class="mt-auto">
                        <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

<section class="row g-3">
    <div class="col-12 col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="feature-icon bg-primary bg-opacity-10 text-primary mb-3">
                    <i class="bi bi-journal-bookmark" style="font-size:1.2rem"></i>
                </div>
                <h5 class="card-title">Organized Catalog</h5>
                <p class="text-muted mb-0">Search books by title, author, or category; track availability in real-time.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="feature-icon bg-primary bg-opacity-10 text-primary mb-3">
                    <i class="bi bi-cloud-arrow-down" style="font-size:1.2rem"></i>
                </div>
                <h5 class="card-title">Digital Resources</h5>
                <p class="text-muted mb-0">Access approved e-books and research files anywhere, anytime.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="feature-icon bg-primary bg-opacity-10 text-primary mb-3">
                    <i class="bi bi-bell" style="font-size:1.2rem"></i>
                </div>
                <h5 class="card-title">Smart Reminders</h5>
                <p class="text-muted mb-0">Email alerts for due dates, overdue notices, and renewal options.</p>
            </div>
        </div>
    </div>
</section>
@endsection


