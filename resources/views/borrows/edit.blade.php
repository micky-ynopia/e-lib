@extends('layouts.app')

@section('title', 'Edit Borrow')

@section('content')
<h1>Edit Borrow</h1>

@if ($errors->any())
    <div style="background:#fef2f2; border:1px solid #ef4444; color:#991b1b; padding:10px 12px; margin:12px 0;">
        <ul style="margin:0; padding-left:16px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('borrows.update', $borrow) }}" style="display:grid; gap:12px; max-width:620px;">
    @csrf
    @method('PUT')
    <div>
        <label>User</label>
        <input disabled value="{{ $borrow->user?->name }}" style="width:100%; padding:8px; border:1px solid #e5e7eb; background:#f9fafb;">
    </div>
    <div>
        <label>Book</label>
        <input disabled value="{{ $borrow->book?->title }}" style="width:100%; padding:8px; border:1px solid #e5e7eb; background:#f9fafb;">
    </div>
    <div>
        <label>Borrowed date</label>
        <input type="date" name="borrowed_at" value="{{ old('borrowed_at', $borrow->borrowed_at->toDateString()) }}" style="width:100%; padding:8px; border:1px solid #e5e7eb;">
    </div>
    <div>
        <label>Due date</label>
        <input type="date" name="due_at" value="{{ old('due_at', $borrow->due_at->toDateString()) }}" style="width:100%; padding:8px; border:1px solid #e5e7eb;">
    </div>
    <div>
        <label>Returned date</label>
        <input type="date" name="returned_at" value="{{ old('returned_at', $borrow->returned_at?->toDateString()) }}" style="width:100%; padding:8px; border:1px solid #e5e7eb;">
    </div>
    <div>
        <label>Status</label>
        <select name="status" style="width:100%; padding:8px; border:1px solid #e5e7eb; text-transform:capitalize;">
            @foreach (['borrowed','returned','overdue'] as $status)
                <option value="{{ $status }}" @selected(old('status', $borrow->status)===$status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div style="display:flex; gap:8px;">
        <button type="submit" style="padding:8px 12px; background:#3b82f6; color:white; border:none; border-radius:4px;">Update</button>
        <a href="{{ route('borrows.index') }}">Cancel</a>
    </div>
    </form>
@endsection


