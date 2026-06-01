<?php

namespace App\Filament\Resources\Forms\RelationManagers;

use App\Models\Form;
use App\Models\User;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        $user = auth()->user();

        if (! $user instanceof User || ! $ownerRecord instanceof Form) {
            return false;
        }

        return $user->can('view', $ownerRecord);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('submission_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('notification_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('submitted_at', 'desc');
    }
}
