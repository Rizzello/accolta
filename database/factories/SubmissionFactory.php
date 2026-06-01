<?php

namespace Database\Factories;

use App\Enums\NotificationStatus;
use App\Enums\SubmissionStatus;
use App\Models\Form;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array{
     *     form_id: FormFactory,
     *     fields: array{email: array{label: string, value: string}},
     *     meta: null,
     *     submission_status: SubmissionStatus,
     *     notification_status: NotificationStatus,
     *     notification_error: null,
     *     submitted_at: Carbon
     * }
     */
    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'fields' => [
                'email' => [
                    'label' => 'Email',
                    'value' => fake()->safeEmail(),
                ],
            ],
            'meta' => null,
            'submission_status' => SubmissionStatus::New,
            'notification_status' => NotificationStatus::NotRequired,
            'notification_error' => null,
            'submitted_at' => now(),
        ];
    }
}
