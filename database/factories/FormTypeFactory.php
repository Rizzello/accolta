<?php

namespace Database\Factories;

use App\Enums\FieldType;
use App\Models\FormType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<FormType>
 */
class FormTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array{
     *     name: string,
     *     slug: string,
     *     description: string,
     *     fields: list<array{name: string, label: string, type: string, rules: list<string>}>,
     *     is_active: bool
     * }
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->sentence(),
            'fields' => [
                [
                    'name' => 'email',
                    'label' => 'Email',
                    'type' => FieldType::Email->value,
                    'rules' => ['required', 'email'],
                ],
            ],
            'is_active' => true,
        ];
    }
}
