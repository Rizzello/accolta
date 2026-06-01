<?php

namespace App\Services\Forms;

use App\Enums\NotificationStatus;
use App\Enums\SubmissionStatus;
use App\Jobs\SendSubmissionNotificationJob;
use App\Models\Form;
use App\Models\Submission;
use Illuminate\Http\Request;

/**
 * @phpstan-import-type ScalarPayload from FormPayloadValidator
 *
 * @phpstan-type FieldDefinition array{name: string, label: string, type: string, rules: list<string>}
 */
final class FormSubmissionService
{
    /**
     * @param  ScalarPayload  $payload
     */
    public function create(Form $form, array $payload, Request $request): Submission
    {
        $submittedAt = now();

        /** @var Submission $submission */
        $submission = Submission::query()->create([
            'form_id' => $form->id,
            'fields' => $this->fieldsWithLabels($form, $payload),
            'meta' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'origin' => $this->stringHeader($request, 'origin'),
                'referer' => $this->stringHeader($request, 'referer'),
                'submitted_at' => $submittedAt->toIso8601String(),
            ],
            'submission_status' => SubmissionStatus::New,
            'notification_status' => NotificationStatus::NotRequired,
            'submitted_at' => $submittedAt,
        ]);

        if ($form->mail_recipients !== null && $form->mail_recipients !== []) {
            SendSubmissionNotificationJob::dispatch($submission);
        }

        return $submission;
    }

    /**
     * @param  ScalarPayload  $payload
     * @return array<string, array{label: string, value: bool|float|int|string|null}>
     */
    private function fieldsWithLabels(Form $form, array $payload): array
    {
        $fields = [];

        foreach ($this->fieldsFor($form) as $field) {
            if (! array_key_exists($field['name'], $payload)) {
                continue;
            }

            $fields[$field['name']] = [
                'label' => $field['label'],
                'value' => $payload[$field['name']],
            ];
        }

        return $fields;
    }

    private function stringHeader(Request $request, string $key): ?string
    {
        $value = $request->headers->get($key);

        return is_string($value) ? $value : null;
    }

    /**
     * @return list<FieldDefinition>
     */
    private function fieldsFor(Form $form): array
    {
        /** @var list<FieldDefinition> $fields */
        $fields = $form->formType->fields;

        return $fields;
    }
}
