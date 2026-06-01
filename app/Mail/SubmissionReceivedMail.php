<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Submission $submission) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $this->submission->loadMissing('form');

        return new Envelope(
            subject: $this->submission->form->mail_subject
                ?: "Nuova submission: {$this->submission->form->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $this->submission->loadMissing('form');

        return new Content(
            view: 'mail.submission-received',
            with: [
                'submission' => $this->submission,
                'form' => $this->submission->form,
                'fields' => $this->submission->fields,
                'meta' => $this->submission->meta ?? [],
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
