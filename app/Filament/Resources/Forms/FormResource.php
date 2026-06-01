<?php

namespace App\Filament\Resources\Forms;

use App\Filament\Resources\Forms\Pages\CreateForm;
use App\Filament\Resources\Forms\Pages\EditForm;
use App\Filament\Resources\Forms\Pages\ListForms;
use App\Filament\Resources\Forms\Pages\ViewForm;
use App\Filament\Resources\Forms\RelationManagers\SubmissionsRelationManager;
use App\Filament\Resources\Forms\RelationManagers\UsersRelationManager;
use App\Filament\Resources\Forms\Schemas\FormForm;
use App\Filament\Resources\Forms\Schemas\FormInfolist;
use App\Filament\Resources\Forms\Tables\FormsTable;
use App\Models\Form;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FormResource extends Resource
{
    protected static ?string $model = Form::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canAccess(): bool
    {
        return self::currentUser()?->can('viewAny', Form::class) === true;
    }

    public static function canCreate(): bool
    {
        return self::currentUser()?->can('create', Form::class) === true;
    }

    public static function canEdit(Model $record): bool
    {
        return $record instanceof Form
            && self::currentUser()?->can('update', $record) === true;
    }

    public static function canDelete(Model $record): bool
    {
        return $record instanceof Form
            && self::currentUser()?->can('delete', $record) === true;
    }

    public static function canDeleteAny(): bool
    {
        return self::currentUser()?->is_admin === true;
    }

    public static function canView(Model $record): bool
    {
        $user = self::currentUser();

        if (! $user instanceof User) {
            return false;
        }

        return $record instanceof Form
            && $user->can('view', $record);
    }

    /**
     * @return Builder<Form>
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var Builder<Form> $query */
        $user = self::currentUser();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        return $query->visibleTo($user);
    }

    public static function form(Schema $schema): Schema
    {
        return FormForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FormInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormsTable::configure($table);
    }

    /**
     * @return array<int, class-string>
     */
    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
            SubmissionsRelationManager::class,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListForms::route('/'),
            'create' => CreateForm::route('/create'),
            'view' => ViewForm::route('/{record}'),
            'edit' => EditForm::route('/{record}/edit'),
        ];
    }

    private static function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
