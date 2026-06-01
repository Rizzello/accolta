<?php

namespace App\Models;

use App\Enums\FormStatus;
use Database\Factories\FormFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $form_type_id
 * @property string $name
 * @property string|null $slug
 * @property string|null $description
 * @property FormStatus $status
 * @property string|null $mail_subject
 * @property array<int, string>|null $mail_recipients
 * @property string $success_message
 * @property string $validation_error_message
 * @property string $closed_message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read FormType $formType
 * @property-read Collection<int, User> $users
 * @property-read Collection<int, Submission> $submissions
 *
 * @method static Builder<Form> visibleTo(User $user)
 */
#[Fillable([
    'form_type_id',
    'name',
    'slug',
    'description',
    'status',
    'mail_subject',
    'mail_recipients',
    'success_message',
    'validation_error_message',
    'closed_message',
])]
class Form extends Model
{
    /** @use HasFactory<FormFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'open',
        'success_message' => 'Grazie, la tua richiesta è stata inviata correttamente.',
        'validation_error_message' => 'Controlla i dati inseriti.',
        'closed_message' => 'Il form è chiuso.',
    ];

    /**
     * @return BelongsTo<FormType, $this>
     */
    public function formType(): BelongsTo
    {
        return $this->belongsTo(FormType::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * @return HasMany<Submission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * @param  Builder<Form>  $query
     * @return Builder<Form>
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->is_admin) {
            return $query;
        }

        return $query->whereHas('users', fn (Builder $usersQuery): Builder => $usersQuery->whereKey($user->id));
    }

    public function isVisibleTo(User $user): bool
    {
        return $user->is_admin || $this->users()->whereKey($user->id)->exists();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mail_recipients' => 'array',
            'status' => FormStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Form $form): void {
            if (blank($form->uuid)) {
                $form->uuid = (string) Str::uuid();
            }
        });
    }
}
