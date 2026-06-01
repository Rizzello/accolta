<?php

namespace Tests\Feature;

use App\Filament\Resources\Forms\FormResource;
use App\Filament\Resources\FormTypes\FormTypeResource;
use App\Filament\Resources\Submissions\SubmissionResource;
use App\Models\Form;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_everything(): void
    {
        $admin = User::factory()->admin()->create();
        $firstForm = Form::factory()->create();
        $secondForm = Form::factory()->create();
        Submission::factory()->for($firstForm)->create();
        Submission::factory()->for($secondForm)->create();

        $this->actingAs($admin);

        $this->assertTrue(FormTypeResource::canAccess());
        $this->assertSame(2, FormResource::getEloquentQuery()->count());
        $this->assertSame(2, SubmissionResource::getEloquentQuery()->count());
    }

    public function test_non_admin_only_sees_associated_forms_and_submissions(): void
    {
        $user = User::factory()->create();
        $visibleForm = Form::factory()->create();
        $hiddenForm = Form::factory()->create();
        $visibleSubmission = Submission::factory()->for($visibleForm)->create();
        $hiddenSubmission = Submission::factory()->for($hiddenForm)->create();

        $visibleForm->users()->attach($user);

        $this->actingAs($user);

        $this->assertFalse(FormTypeResource::canAccess());
        $this->assertSame([$visibleForm->id], FormResource::getEloquentQuery()->orderBy('id')->pluck('id')->all());
        $this->assertSame([$visibleSubmission->id], SubmissionResource::getEloquentQuery()->orderBy('id')->pluck('id')->all());
        $this->assertTrue($user->can('view', $visibleSubmission));
        $this->assertFalse($user->can('view', $hiddenSubmission));
    }
}
