<?php

namespace Database\Seeders;

use App\Enums\FieldType;
use App\Models\FormType;
use Illuminate\Database\Seeder;

/**
 * @phpstan-type FieldDefinition array{name: string, label: string, type: string, rules: list<string>}
 * @phpstan-type FormTypeDefinition array{name: string, description: string, fields: list<FieldDefinition>}
 */
class FormTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var array<string, FormTypeDefinition> $formTypes */
        $formTypes = [
            'contact' => [
                'name' => 'Contact',
                'description' => 'Modulo contatto generico.',
                'fields' => [
                    [
                        'name' => 'full_name',
                        'label' => 'Nome e cognome',
                        'type' => FieldType::String->value,
                        'rules' => ['required', 'string', 'max:255'],
                    ],
                    [
                        'name' => 'email',
                        'label' => 'Email',
                        'type' => FieldType::Email->value,
                        'rules' => ['required', 'email', 'max:255'],
                    ],
                    [
                        'name' => 'subject',
                        'label' => 'Oggetto',
                        'type' => FieldType::String->value,
                        'rules' => ['required', 'string', 'max:255'],
                    ],
                    [
                        'name' => 'message',
                        'label' => 'Messaggio',
                        'type' => FieldType::Text->value,
                        'rules' => ['required', 'string', 'max:5000'],
                    ],
                ],
            ],
            'cfp' => [
                'name' => 'Call for Papers',
                'description' => 'Raccolta proposte talk per eventi e meetup.',
                'fields' => [
                    [
                        'name' => 'full_name',
                        'label' => 'Nome e cognome',
                        'type' => FieldType::String->value,
                        'rules' => ['required', 'string', 'max:255'],
                    ],
                    [
                        'name' => 'email',
                        'label' => 'Email',
                        'type' => FieldType::Email->value,
                        'rules' => ['required', 'email', 'max:255'],
                    ],
                    [
                        'name' => 'talk_title',
                        'label' => 'Titolo talk',
                        'type' => FieldType::String->value,
                        'rules' => ['required', 'string', 'max:255'],
                    ],
                    [
                        'name' => 'talk_abstract',
                        'label' => 'Abstract',
                        'type' => FieldType::Text->value,
                        'rules' => ['required', 'string', 'max:5000'],
                    ],
                    [
                        'name' => 'speaker_bio',
                        'label' => 'Bio speaker',
                        'type' => FieldType::Text->value,
                        'rules' => ['nullable', 'string', 'max:3000'],
                    ],
                    [
                        'name' => 'company',
                        'label' => 'Azienda / organizzazione',
                        'type' => FieldType::String->value,
                        'rules' => ['nullable', 'string', 'max:255'],
                    ],
                    [
                        'name' => 'notes',
                        'label' => 'Note',
                        'type' => FieldType::Text->value,
                        'rules' => ['nullable', 'string', 'max:3000'],
                    ],
                ],
            ],
            'sponsor' => [
                'name' => 'Sponsor',
                'description' => 'Raccolta richieste e contatti sponsor.',
                'fields' => [
                    [
                        'name' => 'company_name',
                        'label' => 'Nome azienda',
                        'type' => FieldType::String->value,
                        'rules' => ['required', 'string', 'max:255'],
                    ],
                    [
                        'name' => 'contact_name',
                        'label' => 'Referente',
                        'type' => FieldType::String->value,
                        'rules' => ['required', 'string', 'max:255'],
                    ],
                    [
                        'name' => 'email',
                        'label' => 'Email',
                        'type' => FieldType::Email->value,
                        'rules' => ['required', 'email', 'max:255'],
                    ],
                    [
                        'name' => 'website',
                        'label' => 'Sito web',
                        'type' => FieldType::Url->value,
                        'rules' => ['nullable', 'url', 'max:255'],
                    ],
                    [
                        'name' => 'message',
                        'label' => 'Messaggio',
                        'type' => FieldType::Text->value,
                        'rules' => ['nullable', 'string', 'max:5000'],
                    ],
                ],
            ],
            'partner' => [
                'name' => 'Partner',
                'description' => 'Raccolta proposte di collaborazione da organizzazioni esterne.',
                'fields' => [
                    [
                        'name' => 'organization_name',
                        'label' => 'Nome organizzazione',
                        'type' => FieldType::String->value,
                        'rules' => ['required', 'string', 'max:255'],
                    ],
                    [
                        'name' => 'contact_name',
                        'label' => 'Referente',
                        'type' => FieldType::String->value,
                        'rules' => ['required', 'string', 'max:255'],
                    ],
                    [
                        'name' => 'email',
                        'label' => 'Email',
                        'type' => FieldType::Email->value,
                        'rules' => ['required', 'email', 'max:255'],
                    ],
                    [
                        'name' => 'website',
                        'label' => 'Sito web',
                        'type' => FieldType::Url->value,
                        'rules' => ['nullable', 'url', 'max:255'],
                    ],
                    [
                        'name' => 'collaboration_proposal',
                        'label' => 'Proposta di collaborazione',
                        'type' => FieldType::Text->value,
                        'rules' => ['nullable', 'string', 'max:5000'],
                    ],
                ],
            ],
        ];

        foreach ($formTypes as $slug => $formType) {
            FormType::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $formType['name'],
                    'description' => $formType['description'],
                    'fields' => $formType['fields'],
                    'is_active' => true,
                ],
            );
        }
    }
}
