<?php

namespace App\Http\Controllers\Api;

use App\Enums\FormStatus;
use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Services\Forms\FormPayloadValidator;
use App\Services\Forms\FormSubmissionService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class SubmitFormController extends Controller
{
    public function __invoke(
        Request $request,
        string $uuid,
        FormPayloadValidator $validator,
        FormSubmissionService $submissions,
    ): JsonResponse {
        $form = Form::query()
            ->with('formType')
            ->where('uuid', $uuid)
            ->first();

        if (! $form instanceof Form) {
            return ApiResponse::error(
                'Form non trovato.',
                null,
                Response::HTTP_NOT_FOUND,
            );
        }

        if ($form->status === FormStatus::Closed) {
            return ApiResponse::error(
                $form->closed_message,
                null,
                Response::HTTP_FORBIDDEN,
            );
        }

        try {
            $payload = $validator->validate($form, $request);
        } catch (ValidationException $exception) {
            return ApiResponse::error(
                $form->validation_error_message,
                $this->validationErrors($exception),
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $submission = $submissions->create($form, $payload, $request);

        return ApiResponse::success(
            $form->success_message,
            ['submission_id' => $submission->uuid],
            Response::HTTP_CREATED,
        );
    }

    /**
     * @return array<string, list<string>>
     */
    private function validationErrors(ValidationException $exception): array
    {
        $errors = [];

        foreach ($exception->errors() as $field => $messages) {
            if (! is_string($field)) {
                continue;
            }

            $fieldMessages = [];

            if (! is_iterable($messages)) {
                continue;
            }

            foreach ($messages as $message) {
                if (is_string($message)) {
                    $fieldMessages[] = $message;
                }
            }

            $errors[$field] = $fieldMessages;
        }

        return $errors;
    }
}
