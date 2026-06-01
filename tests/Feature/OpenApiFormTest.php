<?php

namespace Tests\Feature;

use App\Models\Form;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpenApiFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_openapi_json_is_public(): void
    {
        $form = Form::factory()->create();

        $this->getJson("/api/forms/{$form->uuid}/openapi.json")
            ->assertOk()
            ->assertJsonPath('openapi', '3.0.3')
            ->assertJsonPath("paths./api/forms/{$form->uuid}/submissions.post.requestBody.required", true)
            ->assertJsonPath("paths./api/forms/{$form->uuid}/submissions.post.requestBody.content.application/json.schema.additionalProperties", false);
    }

    public function test_swagger_page_is_public(): void
    {
        $form = Form::factory()->create();

        $this->get("/forms/{$form->uuid}/swagger")
            ->assertOk()
            ->assertSee("/api/forms/{$form->uuid}/openapi.json");
    }
}
