<?php

namespace App\Jobs;

use App\Enums\NotificationStatus;
use App\Mail\SubmissionReceivedMail;
use App\Models\Submission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class SendSubmissionNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Submission $submission) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->submission->loadMissing('form');

        $recipients = $this->submission->form->mail_recipients;

        if ($recipients === null || $recipients === []) {
            $this->submission->update([
                'notification_status' => NotificationStatus::NotRequired,
                'notification_error' => null,
            ]);

            return;
        }

        try {
            Mail::to($recipients)->send(new SubmissionReceivedMail($this->submission));

            $this->submission->update([
                'notification_status' => NotificationStatus::Sent,
                'notification_error' => null,
            ]);
        } catch (Throwable $exception) {
            $this->submission->update([
                'notification_status' => NotificationStatus::Failed,
                'notification_error' => Str::limit($exception->getMessage(), 1000),
            ]);
        }
    }
}
