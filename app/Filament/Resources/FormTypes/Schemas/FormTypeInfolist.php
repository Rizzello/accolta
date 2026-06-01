<?php

namespace App\Filament\Resources\FormTypes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('slug')
                            ->badge(),
                        IconEntry::make('is_active')
                            ->boolean(),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Fields')
                    ->schema([
                        RepeatableEntry::make('fields')
                            ->schema([
                                TextEntry::make('name')
                                    ->badge(),
                                TextEntry::make('label'),
                                TextEntry::make('type')
                                    ->badge(),
                                TextEntry::make('rules')
                                    ->badge()
                                    ->listWithLineBreaks(),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }
}
