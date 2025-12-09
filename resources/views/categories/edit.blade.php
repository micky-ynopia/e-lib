@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<h1>Edit Category</h1>

@if ($errors->any())
    <div style="background:#fef2f2; border:1px solid #ef4444; color:#991b1b; padding:10px 12px; margin:12px 0;">
        <ul style="margin:0; padding-left:16px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('categories.update', $category) }}" style="display:grid; gap:12px; max-width:520px;">
    @csrf
    @method('PUT')
    <div>
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}" style="width:100%; padding:8px; border:1px solid #e5e7eb;">
    </div>
    <div>
        <label>Description</label>
        <textarea name="description" rows="4" style="width:100%; padding:8px; border:1px solid #e5e7eb;">{{ old('description', $category->description) }}</textarea>
    </div>
    <div style="display:flex; gap:8px;">
        <button type="submit" style="padding:8px 12px; background:#3b82f6; color:white; border:none; border-radius:4px;">Update</button>
        <a href="{{ route('categories.index') }}">Cancel</a>
    </div>
    </form>
@endsection


