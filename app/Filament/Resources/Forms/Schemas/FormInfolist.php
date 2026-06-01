<?php

namespace App\Filament\Resources\Forms\Schemas;

use App\Models\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('formType.name')
                            ->label('Form type'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('slug')
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('Endpoints')
                    ->schema([
                        TextEntry::make('submit_endpoint')
                            ->state(fn (Form $record): string => url("/api/forms/{$record->uuid}/submissions"))
                            ->copyable(),
                        TextEntry::make('openapi_url')
                            ->label('OpenAPI URL')
                            ->state(fn (Form $record): string => url("/api/forms/{$record->uuid}/openapi.json"))
                            ->copyable(),
                        TextEntry::make('swagger_url')
                            ->label('Swagger URL')
                            ->state(fn (Form $record): string => url("/forms/{$record->uuid}/swagger"))
                            ->copyable(),
                    ])
                    ->columns(1),
                Section::make('Notifications')
                    ->schema([
                        TextEntry::make('mail_subject')
                            ->placeholder('-'),
                        TextEntry::make('mail_recipients')
                            ->listWithLineBreaks()
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
