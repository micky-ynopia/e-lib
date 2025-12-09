<x-mail::message>
# Overdue notice

Hello {{ $user->name }},

Our records show the following item is now overdue:

- Book: **{{ $book->title }}**
- Due date: **{{ $borrow->due_at->format('F d, Y') }}**
- Days overdue: **{{ now()->diffInDays($borrow->due_at) }}**

<x-mail::button :url="url('/borrows/'.$borrow->id)">
Resolve Now
</x-mail::button>

Please return the book as soon as possible or contact the library to discuss your options.

Thanks,
{{ config('app.name') }}
</x-mail::message>
