<?php

namespace Database\Factories;

use App\Enums\FormStatus;
use App\Models\Form;
use App\Models\FormType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Form>
 */
class FormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array{
     *     form_type_id: FormTypeFactory,
     *     name: string,
     *     slug: string,
     *     description: string,
     *     status: FormStatus,
     *     mail_subject: null,
     *     mail_recipients: null,
     *     success_message: string,
     *     validation_error_message: string,
     *     closed_message: string
     * }
     */
    public function definition(): array
    {
        return [
            'form_type_id' => FormType::factory(),
            'name' => fake()->company(),
            'slug' => fake()->slug(),
            'description' => fake()->sentence(),
            'status' => FormStatus::Open,
            'mail_subject' => null,
            'mail_recipients' => null,
            'success_message' => 'Grazie, la tua richiesta è stata inviata correttamente.',
            'validation_error_message' => 'Controlla i dati inseriti.',
            'closed_message' => 'Il form è chiuso.',
        ];
    }
}
