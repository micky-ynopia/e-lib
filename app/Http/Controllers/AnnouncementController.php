<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admins see all announcements
            $announcements = Announcement::with('creator')->latest()->paginate(10);
        } else {
            // Students and staff see only published announcements
            $announcements = Announcement::published()->with('creator')->latest()->paginate(10);
        }

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return view('announcements.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:announcement,event,notice',
            'priority' => 'required|in:low,medium,high,urgent',
            'is_published' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        Announcement::create($validated);

        return redirect()->route('announcements.index')
            ->with('status', 'Announcement created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        $user = Auth::user();
        
        // Students and staff can only see published announcements
        if (!$user->isAdmin() && !$announcement->isPublished()) {
            abort(404, 'Announcement not found.');
        }

        $announcement->load('creator');
        return view('announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return view('announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:announcement,event,notice',
            'priority' => 'required|in:low,medium,high,urgent',
            'is_published' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
        ]);

        // Update published_at if publishing status changed
        if ($validated['is_published'] && !$announcement->is_published) {
            $validated['published_at'] = now();
        } elseif (!$validated['is_published'] && $announcement->is_published) {
            $validated['published_at'] = null;
        }

        $announcement->update($validated);

        return redirect()->route('announcements.index')
            ->with('status', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('status', 'Announcement deleted successfully.');
    }
}
