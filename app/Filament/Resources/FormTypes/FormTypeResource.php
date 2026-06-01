<?php

namespace App\Filament\Resources\FormTypes;

use App\Filament\Resources\FormTypes\Pages\CreateFormType;
use App\Filament\Resources\FormTypes\Pages\EditFormType;
use App\Filament\Resources\FormTypes\Pages\ListFormTypes;
use App\Filament\Resources\FormTypes\Pages\ViewFormType;
use App\Filament\Resources\FormTypes\Schemas\FormTypeForm;
use App\Filament\Resources\FormTypes\Schemas\FormTypeInfolist;
use App\Filament\Resources\FormTypes\Tables\FormTypesTable;
use App\Models\FormType;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FormTypeResource extends Resource
{
    protected static ?string $model = FormType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Form types';

    protected static ?string $modelLabel = 'form type';

    protected static ?string $pluralModelLabel = 'form types';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->can('viewAny', FormType::class);
    }

    public static function form(Schema $schema): Schema
    {
        return FormTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FormTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormTypesTable::configure($table);
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
            'index' => ListFormTypes::route('/'),
            'create' => CreateFormType::route('/create'),
            'view' => ViewFormType::route('/{record}'),
            'edit' => EditFormType::route('/{record}/edit'),
        ];
    }

    public static function protectedDeleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->before(function (DeleteAction $action, FormType $record): void {
                if (! $record->forms()->exists()) {
                    return;
                }

                Notification::make()
                    ->title('Form type non eliminabile')
                    ->body('Esistono form associati a questo form type.')
                    ->danger()
                    ->send();

                $action->cancel();
            });
    }
}
