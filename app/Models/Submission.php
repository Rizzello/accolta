<?php

namespace App\Models;

use App\Enums\NotificationStatus;
use App\Enums\SubmissionStatus;
use Database\Factories\SubmissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $form_id
 * @property array<string, array{label: string, value: mixed}> $fields
 * @property array<string, mixed>|null $meta
 * @property SubmissionStatus $submission_status
 * @property NotificationStatus $notification_status
 * @property string|null $notification_error
 * @property Carbon $submitted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Form $form
 *
 * @method static Builder<Submission> visibleTo(User $user)
 */
#[Fillable([
    'form_id',
    'fields',
    'meta',
    'submission_status',
    'notification_status',
    'notification_error',
    'submitted_at',
])]
class Submission extends Model
{
    /** @use HasFactory<SubmissionFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'submission_status' => 'new',
        'notification_status' => 'not_required',
    ];

    /**
     * @return BelongsTo<Form, $this>
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * @param  Builder<Submission>  $query
     * @return Builder<Submission>
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->is_admin) {
            return $query;
        }

        return $query->whereHas('form.users', fn (Builder $usersQuery): Builder => $usersQuery->whereKey($user->id));
    }

    public function isVisibleTo(User $user): bool
    {
        return $user->is_admin || $this->form->isVisibleTo($user);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'meta' => 'array',
            'submission_status' => SubmissionStatus::class,
            'notification_status' => NotificationStatus::class,
            'submitted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Submission $submission): void {
            if (blank($submission->uuid)) {
                $submission->uuid = (string) Str::uuid();
            }
        });
    }
}
