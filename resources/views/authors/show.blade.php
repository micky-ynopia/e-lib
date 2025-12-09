@extends('layouts.app')

@section('title', $author->last_name)

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <h1>{{ $author->first_name }} {{ $author->last_name }}</h1>
    <a href="{{ route('authors.edit', $author) }}">Edit</a>
    </div>

<p>{{ $author->bio }}</p>

<h3 style="margin-top:16px;">Books</h3>
<ul>
@forelse ($author->books as $book)
    <li>{{ $book->title }} ({{ $book->published_year }})</li>
@empty
    <li>No books yet.</li>
@endforelse
</ul>

<div style="margin-top:16px;">
    <a href="{{ route('authors.index') }}">Back</a>
</div>
@endsection


