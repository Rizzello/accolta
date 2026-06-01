<?php

namespace App\Services\Forms;

use App\Enums\FieldType;
use App\Models\Form;

/**
 * @phpstan-type FieldDefinition array{name: string, label: string, type: string, rules: list<string>}
 * @phpstan-type SchemaProperty array{type: string, description: string, format?: string, example?: bool|int|string}
 */
final class OpenApiSchemaGenerator
{
    /**
     * @return array<string, mixed>
     */
    public function generate(Form $form): array
    {
        $submitPath = "/api/forms/{$form->uuid}/submissions";
        $requestSchema = $this->requestSchema($form);

        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => $form->name,
                'description' => $form->description ?? '',
                'version' => '1.0.0',
            ],
            'paths' => [
                $submitPath => [
                    'post' => [
                        'summary' => "Invia una submission per {$form->name}",
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => $requestSchema,
                                    'example' => $this->requestExample($form),
                                ],
                            ],
                        ],
                        'responses' => $this->responses($form),
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array{
     *     type: string,
     *     additionalProperties: false,
     *     required: list<string>,
     *     properties: array<string, SchemaProperty>
     * }
     */
    private function requestSchema(Form $form): array
    {
        $required = [];
        $properties = [];

        foreach ($this->fieldsFor($form) as $field) {
            if (in_array('required', $field['rules'], true)) {
                $required[] = $field['name'];
            }

            $properties[$field['name']] = $this->propertyFor($field);
        }

        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => $required,
            'properties' => $properties,
        ];
    }

    /**
     * @param  FieldDefinition  $field
     * @return SchemaProperty
     */
    private function propertyFor(array $field): array
    {
        $description = $field['label'];
        $example = $this->exampleFor($field);

        return match (FieldType::tryFrom($field['type'])) {
            FieldType::Email => [
                'type' => 'string',
                'format' => 'email',
                'description' => $description,
                'example' => $example,
            ],
            FieldType::Url => [
                'type' => 'string',
                'format' => 'uri',
                'description' => $description,
                'example' => $example,
            ],
            FieldType::Number => [
                'type' => 'number',
                'description' => $description,
                'example' => $example,
            ],
            FieldType::Boolean => [
                'type' => 'boolean',
                'description' => $description,
                'example' => $example,
            ],
            default => [
                'type' => 'string',
                'description' => $description,
                'example' => $example,
            ],
        };
    }

    /**
     * @return array<string, bool|int|string>
     */
    private function requestExample(Form $form): array
    {
        $example = [];

        foreach ($this->fieldsFor($form) as $field) {
            $example[$field['name']] = $this->exampleFor($field);
        }

        return $example;
    }

    /**
     * @param  FieldDefinition  $field
     */
    private function exampleFor(array $field): bool|int|string
    {
        return match (FieldType::tryFrom($field['type'])) {
            FieldType::Email => 'mario@example.com',
            FieldType::Url => 'https://example.com',
            FieldType::Number => 42,
            FieldType::Boolean => true,
            FieldType::Text => 'Vorrei informazioni.',
            default => match ($field['name']) {
                'full_name', 'contact_name' => 'Mario Rossi',
                'company_name' => 'Acme Srl',
                'organization_name' => 'Example Org',
                'subject' => 'Richiesta informazioni',
                default => 'Valore di esempio',
            },
        };
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function responses(Form $form): array
    {
        return [
            '201' => [
                'description' => 'Submission creata.',
                'content' => [
                    'application/json' => [
                        'schema' => $this->successResponseSchema(),
                        'example' => [
                            'success' => true,
                            'message' => $form->success_message,
                            'data' => [
                                'submission_id' => '8f5baf64-89a1-41b4-9e5a-75a7b4dfd9f5',
                            ],
                        ],
                    ],
                ],
            ],
            '403' => [
                'description' => 'Form chiuso.',
                'content' => [
                    'application/json' => [
                        'schema' => $this->errorResponseSchema(nullableErrors: true),
                        'example' => [
                            'success' => false,
                            'message' => $form->closed_message,
                            'errors' => null,
                        ],
                    ],
                ],
            ],
            '422' => [
                'description' => 'Validazione fallita.',
                'content' => [
                    'application/json' => [
                        'schema' => $this->errorResponseSchema(nullableErrors: false),
                        'example' => [
                            'success' => false,
                            'message' => $form->validation_error_message,
                            'errors' => [
                                'email' => [
                                    'The email field is required.',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '429' => [
                'description' => 'Rate limit superato.',
                'content' => [
                    'application/json' => [
                        'schema' => $this->errorResponseSchema(nullableErrors: true),
                        'example' => [
                            'success' => false,
                            'message' => 'Troppe richieste. Riprova più tardi.',
                            'errors' => null,
                        ],
                    ],
                ],
            ],
            '404' => [
                'description' => 'Form non trovato.',
                'content' => [
                    'application/json' => [
                        'schema' => $this->errorResponseSchema(nullableErrors: true),
                        'example' => [
                            'success' => false,
                            'message' => 'Form non trovato.',
                            'errors' => null,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function successResponseSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['success', 'message', 'data'],
            'properties' => [
                'success' => ['type' => 'boolean'],
                'message' => ['type' => 'string'],
                'data' => [
                    'type' => 'object',
                    'required' => ['submission_id'],
                    'properties' => [
                        'submission_id' => [
                            'type' => 'string',
                            'format' => 'uuid',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function errorResponseSchema(bool $nullableErrors): array
    {
        return [
            'type' => 'object',
            'required' => ['success', 'message', 'errors'],
            'properties' => [
                'success' => ['type' => 'boolean'],
                'message' => ['type' => 'string'],
                'errors' => $nullableErrors
                    ? [
                        'nullable' => true,
                        'type' => 'object',
                    ]
                    : [
                        'type' => 'object',
                        'additionalProperties' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
            ],
        ];
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
