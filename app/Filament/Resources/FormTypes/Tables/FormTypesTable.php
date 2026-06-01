<?php

namespace App\Filament\Resources\FormTypes\Tables;

use App\Filament\Resources\FormTypes\FormTypeResource;
use App\Models\FormType;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FormTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('fields_count')
                    ->label('Fields')
                    ->state(fn (FormType $record): int => count($record->fields))
                    ->sortable(false),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                FormTypeResource::protectedDeleteAction(),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
