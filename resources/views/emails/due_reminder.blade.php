<x-mail::message>
# Due date approaching

Hello {{ $user->name }},

This is a friendly reminder that your borrowed book is coming due.

- Book: **{{ $book->title }}**
- Due date: **{{ $borrow->due_at->format('F d, Y') }}**
- Status: {{ ucfirst($borrow->status) }}

<x-mail::button :url="url('/borrows/'.$borrow->id)">
View Borrow Details
</x-mail::button>

If you need more time and the book is still available, you may request a renewal.

Thanks,
{{ config('app.name') }}
</x-mail::message>
