<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users (admin only)
     */
    public function index(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $query = User::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('course', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->get('role') !== '') {
            $query->where('role', $request->get('role'));
        }

        // Filter by approval status
        if ($request->has('approval_status') && $request->get('approval_status') !== '') {
            if ($request->get('approval_status') === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->get('approval_status') === 'approved') {
                $query->where('is_approved', true);
            }
        }

        $users = $query->orderByDesc('created_at')->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return view('users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:librarian,staff,student',
            'student_id' => 'nullable|string|max:20|unique:users',
            'course' => 'nullable|string|max:100',
            'year_level' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'is_approved' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // Will be hashed automatically by the 'hashed' cast
            'role' => $validated['role'],
            'student_id' => $validated['student_id'] ?? null,
            'course' => $validated['course'] ?? null,
            'year_level' => $validated['year_level'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_approved' => $validated['is_approved'] ?? true,
            'approved_at' => $validated['is_approved'] ?? true ? now() : null,
        ]);

        return redirect()->route('users.index')
            ->with('status', 'User created successfully.');
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $user->load(['borrows.book', 'bookRequests.book']);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing a user
     */
    public function edit(User $user)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update user information
     */
    public function update(Request $request, User $user)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:librarian,staff,student',
            'student_id' => 'nullable|string|max:20|unique:users,student_id,' . $user->id,
            'course' => 'nullable|string|max:100',
            'year_level' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|confirmed|string|min:8',
        ]);

        // Only update password if provided (will be hashed automatically by the 'hashed' cast)
        if (!isset($validated['password']) || empty($validated['password'])) {
            unset($validated['password']);
            unset($validated['password_confirmation']);
        } else {
            unset($validated['password_confirmation']);
            // Password will be hashed automatically by the 'hashed' cast in User model
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('status', 'User updated successfully.');
    }

    /**
     * Approve a user
     */
    public function approve(User $user)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('status', 'User approved successfully.');
    }

    /**
     * Reject a user
     */
    public function reject(User $user)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $user->update([
            'is_approved' => false,
            'approved_at' => null,
        ]);

        return redirect()->back()->with('status', 'User approval revoked.');
    }

    /**
     * Delete a user
     */
    public function destroy(User $user)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Prevent deletion of the currently authenticated user
        if ($user->id === Auth::id()) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete your own account.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('status', 'User deleted successfully.');
    }

    /**
     * Bulk approve users
     */
    public function bulkApprove(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $count = User::whereIn('id', $request->get('user_ids'))
            ->update([
                'is_approved' => true,
                'approved_at' => now(),
            ]);

        return redirect()->back()->with('status', "Successfully approved {$count} users.");
    }

    /**
     * Bulk reject users
     */
    public function bulkReject(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $count = User::whereIn('id', $request->get('user_ids'))
            ->update([
                'is_approved' => false,
                'approved_at' => null,
            ]);

        return redirect()->back()->with('status', "Successfully rejected {$count} users.");
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Prevent deletion of currently authenticated user
        $userIds = array_diff($request->get('user_ids'), [Auth::id()]);

        $count = User::whereIn('id', $userIds)->delete();

        return redirect()->back()->with('status', "Successfully deleted {$count} users.");
    }
}

