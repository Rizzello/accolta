<?php

namespace App\Filament\Resources\Forms\Schemas;

use App\Enums\FormStatus;
use App\Models\FormType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class FormForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->maxLength(255),
                        Select::make('form_type_id')
                            ->label('Form type')
                            ->relationship(
                                name: 'formType',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query): Builder {
                                    /** @var Builder<FormType> $query */
                                    return self::activeFormTypes($query);
                                },
                            )
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('status')
                            ->required()
                            ->options(FormStatus::class)
                            ->default(FormStatus::Open),
                        Textarea::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Notifications')
                    ->schema([
                        TextInput::make('mail_subject')
                            ->maxLength(255),
                        TagsInput::make('mail_recipients')
                            ->nestedRecursiveRules(['email'])
                            ->placeholder('email@example.com')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Messages')
                    ->schema([
                        Textarea::make('success_message')
                            ->required()
                            ->default('Grazie, la tua richiesta è stata inviata correttamente.'),
                        Textarea::make('validation_error_message')
                            ->required()
                            ->default('Controlla i dati inseriti.'),
                        Textarea::make('closed_message')
                            ->required()
                            ->default('Il form è chiuso.'),
                    ])
                    ->columns(3),
            ]);
    }

    /**
     * @param  Builder<FormType>  $query
     * @return Builder<FormType>
     */
    private static function activeFormTypes(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
