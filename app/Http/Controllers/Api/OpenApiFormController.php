<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Services\Forms\OpenApiSchemaGenerator;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OpenApiFormController extends Controller
{
    public function __invoke(string $uuid, OpenApiSchemaGenerator $generator): JsonResponse
    {
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

        return response()->json($generator->generate($form));
    }
}
