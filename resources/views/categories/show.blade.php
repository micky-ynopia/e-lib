@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <h1>{{ $category->name }}</h1>
    <a href="{{ route('categories.edit', $category) }}">Edit</a>
    </div>

<p>{{ $category->description }}</p>

<h3 style="margin-top:16px;">Books</h3>
<ul>
@forelse ($category->books as $book)
    <li>{{ $book->title }} ({{ $book->published_year }})</li>
@empty
    <li>No books yet.</li>
@endforelse
</ul>

<div style="margin-top:16px;">
    <a href="{{ route('categories.index') }}">Back</a>
    </div>
@endsection


