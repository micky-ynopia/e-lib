<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Library - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @if (function_exists('vite') && (vite()->isRunningHot() || file_exists(public_path('build/manifest.json'))))
        @vite(['resources/css/app.css','resources/js/app.js'])
    @endif
</head>
<body class="antialiased" style="background-color:#f5f8ff;">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#0d6efd;">
        <div class="container">
            <a class="navbar-brand fw-semibold d-flex align-items-center gap-2" href="/">
                <img src="{{ asset('images/nemsu-logo.jpg') }}" alt="NEMSU" style="width:28px;height:28px;object-fit:contain;"/>
                E-Library
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @auth
                        @if(auth()->user()->isLibrarian() || auth()->user()->isStaff())
                            <li class="nav-item"><a class="nav-link" href="{{ route('authors.index') }}">Authors</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('categories.index') }}">Categories</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('books.index') }}">Books</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('borrows.index') }}">Borrows</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('book-requests.index') }}">Requests</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('theses.index') }}">Theses</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('reports.statistics') }}">Reports</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('announcements.index') }}">Announcements</a></li>
                        @else
                            <li class="nav-item"><a class="nav-link" href="{{ route('books.index') }}">Browse Books</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('favorites.index') }}">My Favorites</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('reserves.index') }}">My Reservations</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('book-requests.index') }}">My Requests</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('borrows.index') }}">My Borrows</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('theses.index') }}">Thesis Repository</a></li>
                        @endif
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('books.index') }}">Browse Books</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('theses.index') }}">Thesis Repository</a></li>
                    @endauth
                </ul>
                
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                                <span class="badge bg-light text-dark ms-1">{{ auth()->user()->role_display }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(auth()->user()->isLibrarian())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                                @elseif(auth()->user()->isStaff())
                                    <li><a class="dropdown-item" href="{{ route('staff.dashboard') }}">Staff Dashboard</a></li>
                                @else
                                    <li><a class="dropdown-item" href="{{ route('student.dashboard') }}">Student Dashboard</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    <main class="container py-3">
        @if (session('status'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>


