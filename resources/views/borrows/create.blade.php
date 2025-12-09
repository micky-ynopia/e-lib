@extends('layouts.app')

@section('title', 'New Borrow')

@section('content')
<h1>New Borrow</h1>

@if ($errors->any())
    <div style="background:#fef2f2; border:1px solid #ef4444; color:#991b1b; padding:10px 12px; margin:12px 0;">
        <ul style="margin:0; padding-left:16px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('borrows.store') }}" style="display:grid; gap:12px; max-width:620px;">
    @csrf
    <div>
        <label>User</label>
        <select name="user_id" style="width:100%; padding:8px; border:1px solid #e5e7eb;">
            <option value="">Select user</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(old('user_id')==$user->id)>{{ $user->name }} ({{ $user->email }})</option>
            @endforeach
        </select>
    </div>
    <div>
        <label>Book</label>
        <select name="book_id" style="width:100%; padding:8px; border:1px solid #e5e7eb;">
            <option value="">Select book</option>
            @foreach ($books as $book)
                <option value="{{ $book->id }}" @selected(old('book_id')==$book->id)>{{ $book->title }} ({{ $book->available_copies }} available)</option>
            @endforeach
        </select>
    </div>
    <div>
        <label>Borrowed date</label>
        <input type="date" name="borrowed_at" value="{{ old('borrowed_at', now()->toDateString()) }}" style="width:100%; padding:8px; border:1px solid #e5e7eb;">
    </div>
    <div>
        <label>Due date</label>
        <input type="date" name="due_at" value="{{ old('due_at', now()->addDays(14)->toDateString()) }}" style="width:100%; padding:8px; border:1px solid #e5e7eb;">
    </div>
    <div style="display:flex; gap:8px;">
        <button type="submit" style="padding:8px 12px; background:#10b981; color:white; border:none; border-radius:4px;">Create</button>
        <a href="{{ route('borrows.index') }}">Cancel</a>
    </div>
    </form>
@endsection


