<?php

namespace App\Services\Forms;

use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use stdClass;

/**
 * @phpstan-type FieldDefinition array{name: string, label: string, type: string, rules: list<string>}
 * @phpstan-type ScalarPayload array<string, bool|float|int|string|null>
 */
final class FormPayloadValidator
{
    /**
     * @return ScalarPayload
     *
     * @throws ValidationException
     */
    public function validate(Form $form, Request $request): array
    {
        if (! $request->isJson()) {
            throw ValidationException::withMessages([
                '_payload' => ['Il payload deve essere JSON.'],
            ]);
        }

        $decoded = json_decode($request->getContent());

        if (! $decoded instanceof stdClass) {
            throw ValidationException::withMessages([
                '_payload' => ['Il payload deve essere un oggetto JSON flat.'],
            ]);
        }

        $payload = json_decode($request->getContent(), associative: true);

        /** @var array<string, mixed> $payload */
        $this->rejectNestedPayload($payload);

        $rules = $this->rulesFor($form);
        $unexpectedFields = array_diff(array_keys($payload), array_keys($rules));

        if ($unexpectedFields !== []) {
            throw ValidationException::withMessages([
                '_payload' => ['Sono presenti campi non previsti dallo schema del form.'],
            ]);
        }

        /** @var ScalarPayload $validated */
        $validated = Validator::make($payload, $rules)->validate();

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws ValidationException
     */
    private function rejectNestedPayload(array $payload): void
    {
        foreach ($payload as $value) {
            if (is_array($value) || $value instanceof stdClass) {
                throw ValidationException::withMessages([
                    '_payload' => ['Il payload deve essere flat.'],
                ]);
            }
        }
    }

    /**
     * @return array<string, list<string>>
     */
    private function rulesFor(Form $form): array
    {
        $rules = [];

        foreach ($this->fieldsFor($form) as $field) {
            $rules[$field['name']] = $field['rules'];
        }

        return $rules;
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
