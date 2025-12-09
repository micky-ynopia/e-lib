<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect based on user role
            if ($user->isLibrarian()) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->isStaff()) {
                return redirect()->intended('/staff/dashboard');
            } else {
                return redirect()->intended('/student/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle student registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'student_id' => 'required|string|max:20|unique:users',
            'course' => 'required|string|max:100',
            'year_level' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
        ]);

        // Validate NEMSU email domain
        if (!str_ends_with($validated['email'], '@nemsu.edu.ph')) {
            return back()->withErrors([
                'email' => 'Please use your official NEMSU email address (@nemsu.edu.ph)',
            ])->withInput();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'student_id' => $validated['student_id'],
            'course' => $validated['course'],
            'year_level' => $validated['year_level'],
            'phone' => $validated['phone'],
            'is_approved' => false, // Requires admin approval
        ]);

        Auth::login($user);

        return redirect()->route('student.dashboard')->with('status', 
            'Registration successful! Your account is pending approval from library staff.');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    /**
     * Show student dashboard
     */
    public function studentDashboard()
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            abort(403, 'Access denied. Student access required.');
        }

        $recentBorrows = $user->borrows()->with('book')->latest()->limit(5)->get();
        $pendingRequests = $user->bookRequests()->with('book')->where('status', 'pending')->get();
        
        return view('student.dashboard', compact('recentBorrows', 'pendingRequests'));
    }

    /**
     * Show staff dashboard
     */
    public function staffDashboard()
    {
        $user = Auth::user();
        
        if (!$user->isStaff()) {
            abort(403, 'Access denied. Staff access required.');
        }

        $pendingRequests = \App\Models\BookRequest::with(['user', 'book'])
            ->where('status', 'pending')
            ->latest()
            ->get();
        
        $recentBorrows = \App\Models\Borrow::with(['user', 'book'])
            ->latest()
            ->limit(10)
            ->get();
        
        return view('staff.dashboard', compact('pendingRequests', 'recentBorrows'));
    }

    /**
     * Show admin dashboard
     */
    public function adminDashboard()
    {
        $user = Auth::user();
        
        if (!$user->isLibrarian()) {
            abort(403, 'Access denied. Librarian access required.');
        }

        // Get dashboard statistics
        $stats = [
            'total_books' => \App\Models\Book::count(),
            'total_users' => \App\Models\User::count(),
            'total_borrows' => \App\Models\Borrow::count(),
            'pending_requests' => \App\Models\BookRequest::where('status', 'pending')->count(),
            'overdue_borrows' => \App\Models\Borrow::where('status', 'overdue')->count(),
            'pending_approvals' => \App\Models\User::where('is_approved', false)->count(),
        ];

        $recentAnnouncements = \App\Models\Announcement::latest()->limit(5)->get();
        $mostBorrowedBooks = \App\Models\Book::withCount('borrows')
            ->orderBy('borrows_count', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact('stats', 'recentAnnouncements', 'mostBorrowedBooks'));
    }
}
