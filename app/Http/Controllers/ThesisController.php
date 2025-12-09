<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ThesisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admins see all theses
            $theses = Thesis::with(['author', 'approver'])->latest()->paginate(10);
        } else {
            // Students and staff see only approved theses
            $theses = Thesis::where('status', 'approved')
                ->with(['author', 'approver'])
                ->latest()
                ->paginate(10);
        }

        return view('theses.index', compact('theses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->isStudent()) {
            abort(403, 'Only students can submit theses.');
        }

        return view('theses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isStudent()) {
            abort(403, 'Only students can submit theses.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author_name' => 'required|string|max:255',
            'course' => 'required|string|max:100',
            'year_level' => 'required|string|max:20',
            'academic_year' => 'required|string|max:20',
            'abstract' => 'required|string',
            'keywords' => 'required|string|max:500',
            'thesis_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        // Store the file
        $file = $request->file('thesis_file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('theses', $filename, 'public');

        $thesis = Thesis::create([
            'title' => $validated['title'],
            'author_name' => $validated['author_name'],
            'course' => $validated['course'],
            'year_level' => $validated['year_level'],
            'academic_year' => $validated['academic_year'],
            'abstract' => $validated['abstract'],
            'keywords' => $validated['keywords'],
            'file_path' => $filePath,
            'file_name' => $filename,
            'file_size' => $file->getSize(),
            'status' => 'pending',
            'submitted_by' => Auth::id(),
        ]);

        return redirect()->route('theses.index')
            ->with('status', 'Thesis submitted successfully! It will be reviewed by library staff.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Thesis $thesis)
    {
        $user = Auth::user();
        
        // Students can only see approved theses unless it's their own
        if ($user->isStudent()) {
            if ($thesis->status !== 'approved' && $thesis->submitted_by !== $user->id) {
                abort(403, 'Access denied.');
            }
        }

        $thesis->load(['author', 'approver']);
        return view('theses.show', compact('thesis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thesis $thesis)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return view('theses.edit', compact('thesis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Thesis $thesis)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author_name' => 'required|string|max:255',
            'course' => 'required|string|max:100',
            'year_level' => 'required|string|max:20',
            'academic_year' => 'required|string|max:20',
            'abstract' => 'required|string',
            'keywords' => 'required|string|max:500',
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $validated['approved_by'] = $validated['status'] === 'approved' ? Auth::id() : null;
        $validated['approved_at'] = $validated['status'] === 'approved' ? now() : null;

        $thesis->update($validated);

        return redirect()->route('theses.index')
            ->with('status', 'Thesis updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Thesis $thesis)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Delete the file if it exists
        if ($thesis->file_path && Storage::disk('public')->exists($thesis->file_path)) {
            Storage::disk('public')->delete($thesis->file_path);
        }

        $thesis->delete();

        return redirect()->route('theses.index')
            ->with('status', 'Thesis deleted successfully.');
    }

    /**
     * Download the thesis file
     */
    public function download(Thesis $thesis)
    {
        if ($thesis->status !== 'approved') {
            abort(403, 'This thesis is not available for download.');
        }

        if (!Storage::disk('public')->exists($thesis->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($thesis->file_path, $thesis->file_name);
    }
}
