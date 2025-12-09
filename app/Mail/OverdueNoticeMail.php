<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Borrow;

class OverdueNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Borrow $borrow;

    /**
     * Create a new message instance.
     */
    public function __construct(Borrow $borrow)
    {
        $this->borrow = $borrow->load('book','user');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $title = $this->borrow->book?->title ?? 'your borrowed book';
        return new Envelope(
            subject: 'Overdue Notice: ' . $title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.overdue_notice',
            with: [
                'borrow' => $this->borrow,
                'user' => $this->borrow->user,
                'book' => $this->borrow->book,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
