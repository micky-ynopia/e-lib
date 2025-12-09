@extends('layouts.app')

@section('title', 'Edit Author')

@section('content')
<h1>Edit Author</h1>

@if ($errors->any())
    <div style="background:#fef2f2; border:1px solid #ef4444; color:#991b1b; padding:10px 12px; margin:12px 0;">
        <ul style="margin:0; padding-left:16px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('authors.update', $author) }}" style="display:grid; gap:12px; max-width:520px;">
    @csrf
    @method('PUT')
    <div>
        <label>First name</label>
        <input type="text" name="first_name" value="{{ old('first_name', $author->first_name) }}" style="width:100%; padding:8px; border:1px solid #e5e7eb;">
    </div>
    <div>
        <label>Last name</label>
        <input type="text" name="last_name" value="{{ old('last_name', $author->last_name) }}" style="width:100%; padding:8px; border:1px solid #e5e7eb;">
    </div>
    <div>
        <label>Bio</label>
        <textarea name="bio" rows="4" style="width:100%; padding:8px; border:1px solid #e5e7eb;">{{ old('bio', $author->bio) }}</textarea>
    </div>
    <div style="display:flex; gap:8px;">
        <button type="submit" style="padding:8px 12px; background:#3b82f6; color:white; border:none; border-radius:4px;">Update</button>
        <a href="{{ route('authors.index') }}">Cancel</a>
    </div>
    </form>
@endsection


