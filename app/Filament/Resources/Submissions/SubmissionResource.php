<?php

namespace App\Filament\Resources\Submissions;

use App\Filament\Resources\Submissions\Pages\EditSubmission;
use App\Filament\Resources\Submissions\Pages\ListSubmissions;
use App\Filament\Resources\Submissions\Pages\ViewSubmission;
use App\Filament\Resources\Submissions\Schemas\SubmissionForm;
use App\Filament\Resources\Submissions\Schemas\SubmissionInfolist;
use App\Filament\Resources\Submissions\Tables\SubmissionsTable;
use App\Models\Form;
use App\Models\Submission;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canAccess(): bool
    {
        return self::currentUser()?->can('viewAny', Submission::class) === true;
    }

    public static function canCreate(): bool
    {
        return self::currentUser()?->can('create', Submission::class) === true;
    }

    public static function canDelete(Model $record): bool
    {
        return $record instanceof Submission
            && self::currentUser()?->can('delete', $record) === true;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canView(Model $record): bool
    {
        $user = self::currentUser();

        if (! $user instanceof User) {
            return false;
        }

        return $record instanceof Submission
            && $user->can('view', $record);
    }

    public static function canEdit(Model $record): bool
    {
        return $record instanceof Submission
            && self::currentUser()?->can('update', $record) === true;
    }

    /**
     * @return Builder<Submission>
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('form');

        /** @var Builder<Submission> $query */
        $user = self::currentUser();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        return $query->visibleTo($user);
    }

    public static function form(Schema $schema): Schema
    {
        return SubmissionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubmissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubmissionsTable::configure($table);
    }

    /**
     * @return array<int, class-string>
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSubmissions::route('/'),
            'view' => ViewSubmission::route('/{record}'),
            'edit' => EditSubmission::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function accessibleFormOptions(): array
    {
        $user = self::currentUser();

        if (! $user instanceof User) {
            return [];
        }

        $query = Form::query()
            ->visibleTo($user)
            ->orderBy('name');

        /** @var array<int, string> $options */
        $options = $query->pluck('name', 'id')->all();

        return $options;
    }

    private static function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
