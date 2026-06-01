<?php

namespace App\Filament\Resources\Submissions\Schemas;

use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->schema([
                        TextEntry::make('uuid')
                            ->copyable(),
                        TextEntry::make('form.name')
                            ->label('Form'),
                        TextEntry::make('submission_status')
                            ->badge(),
                        TextEntry::make('notification_status')
                            ->badge(),
                        TextEntry::make('submitted_at')
                            ->dateTime(),
                        TextEntry::make('notification_error')
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('Fields')
                    ->schema([
                        CodeEntry::make('fields')
                            ->columnSpanFull(),
                    ]),
                Section::make('Meta')
                    ->schema([
                        CodeEntry::make('meta')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
