<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Contracts\View\View;

class SwaggerFormViewController extends Controller
{
    public function __invoke(string $uuid): View
    {
        $form = Form::query()
            ->where('uuid', $uuid)
            ->firstOrFail();

        return view('forms.swagger', [
            'form' => $form,
            'openApiUrl' => url("/api/forms/{$form->uuid}/openapi.json"),
            'submitUrl' => url("/api/forms/{$form->uuid}/submissions"),
        ]);
    }
}
