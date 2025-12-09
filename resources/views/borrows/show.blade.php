@extends('layouts.app')

@section('title', 'Borrow #'.$borrow->id)

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <h1>Borrow #{{ $borrow->id }}</h1>
    <a href="{{ route('borrows.edit', $borrow) }}">Edit</a>
    </div>

<ul>
    <li><strong>User:</strong> {{ $borrow->user?->name }} ({{ $borrow->user?->email }})</li>
    <li><strong>Book:</strong> {{ $borrow->book?->title }}</li>
    <li><strong>Borrowed:</strong> {{ $borrow->borrowed_at->format('Y-m-d') }}</li>
    <li><strong>Due:</strong> {{ $borrow->due_at->format('Y-m-d') }}</li>
    <li><strong>Returned:</strong> {{ $borrow->returned_at?->format('Y-m-d') ?? '—' }}</li>
    <li><strong>Status:</strong> {{ ucfirst($borrow->status) }}</li>
    @if($borrow->fine_amount && $borrow->fine_amount > 0)
    <li><strong>Fine Amount:</strong> ₱{{ number_format($borrow->fine_amount, 2) }}</li>
    <li><strong>Fine Calculated:</strong> {{ $borrow->fine_calculated_at?->format('Y-m-d H:i:s') ?? '—' }}</li>
    <li><strong>Fine Paid:</strong> {{ $borrow->fine_paid_at?->format('Y-m-d H:i:s') ?? 'Not paid' }}</li>
    @if($borrow->fine_notes)
    <li><strong>Fine Notes:</strong> {{ $borrow->fine_notes }}</li>
    @endif
    @endif
    </ul>

@auth
@if(auth()->user()->isAdmin() && $borrow->fine_amount && $borrow->fine_amount > 0 && !$borrow->fine_paid_at)
<div style="margin-top:16px; padding:16px; background-color:#f8f9fa; border-radius:4px;">
    <h5>Pay Fine</h5>
    <form method="POST" action="{{ route('borrows.pay-fine', $borrow) }}">
        @csrf
        <div style="margin-bottom:12px;">
            <label for="fine_notes" style="display:block; margin-bottom:4px;">Notes (optional):</label>
            <textarea name="fine_notes" id="fine_notes" rows="3" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;"></textarea>
        </div>
        <button type="submit" class="btn btn-success">
            <i class="bi bi-credit-card"></i> Mark Fine as Paid (₱{{ number_format($borrow->fine_amount, 2) }})
        </button>
    </form>
</div>
@endif
@endauth

<div style="margin-top:16px;">
    <a href="{{ route('borrows.index') }}">Back</a>
    </div>
@endsection


