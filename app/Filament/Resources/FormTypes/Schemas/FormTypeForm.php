<?php

namespace App\Filament\Resources\FormTypes\Schemas;

use App\Enums\FieldType;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FormTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, string $operation, ?string $state): void {
                                if ($operation !== 'create') {
                                    return;
                                }

                                $set('slug', Str::slug($state ?? ''));
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->alphaDash()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->required()
                            ->default(true),
                    ])
                    ->columns(2),
                Section::make('Fields')
                    ->schema([
                        Repeater::make('fields')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->alphaDash()
                                    ->distinct()
                                    ->maxLength(255),
                                TextInput::make('label')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('type')
                                    ->required()
                                    ->options(FieldType::class),
                                Repeater::make('rules')
                                    ->simple(
                                        TextInput::make('rule')
                                            ->required()
                                            ->maxLength(255),
                                    )
                                    ->required()
                                    ->minItems(1)
                                    ->default(['required']),
                            ])
                            ->required()
                            ->minItems(1)
                            ->itemLabel(fn (mixed $state): ?string => is_array($state) ? self::fieldItemLabel($state) : null)
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * @param  array<mixed, mixed>  $state
     */
    private static function fieldItemLabel(array $state): ?string
    {
        $label = $state['label'] ?? $state['name'] ?? null;

        return is_string($label) ? $label : null;
    }
}
