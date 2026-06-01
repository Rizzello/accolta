<?php

namespace Tests\Feature;

use App\Enums\FormStatus;
use App\Enums\NotificationStatus;
use App\Mail\SubmissionReceivedMail;
use App\Models\Form;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class PublicFormSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_accepts_valid_submission(): void
    {
        $form = Form::factory()->create();

        $response = $this->postJson("/api/forms/{$form->uuid}/submissions", [
            'email' => 'mario@example.com',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true);

        $submission = Submission::query()->firstOrFail();

        $this->assertSame('mario@example.com', $submission->fields['email']['value']);
        $this->assertSame(NotificationStatus::NotRequired, $submission->notification_status);
    }

    public function test_it_rejects_closed_invalid_and_unexpected_payloads(): void
    {
        $closedForm = Form::factory()->create(['status' => FormStatus::Closed]);
        $openForm = Form::factory()->create();

        $this->postJson("/api/forms/{$closedForm->uuid}/submissions", ['email' => 'mario@example.com'])
            ->assertForbidden();

        $this->postJson("/api/forms/{$openForm->uuid}/submissions", [])
            ->assertUnprocessable();

        $this->postJson("/api/forms/{$openForm->uuid}/submissions", [
            'email' => 'mario@example.com',
            'unexpected' => 'value',
        ])->assertUnprocessable();

        $this->assertSame(0, Submission::query()->count());
    }

    public function test_it_sends_notification_when_recipients_are_configured(): void
    {
        Mail::fake();

        $form = Form::factory()->create([
            'mail_recipients' => ['admin@example.com'],
        ]);

        $this->postJson("/api/forms/{$form->uuid}/submissions", [
            'email' => 'mario@example.com',
        ])->assertCreated();

        Mail::assertSent(SubmissionReceivedMail::class);

        $this->assertSame(NotificationStatus::Sent, Submission::query()->firstOrFail()->notification_status);
    }

    public function test_email_failure_does_not_block_submission_creation(): void
    {
        Mail::shouldReceive('to')
            ->once()
            ->andThrow(new RuntimeException('SMTP down'));

        $form = Form::factory()->create([
            'mail_recipients' => ['admin@example.com'],
        ]);

        $this->postJson("/api/forms/{$form->uuid}/submissions", [
            'email' => 'mario@example.com',
        ])->assertCreated();

        $submission = Submission::query()->firstOrFail();

        $this->assertSame(NotificationStatus::Failed, $submission->notification_status);
        $this->assertSame('SMTP down', $submission->notification_error);
    }
}
