<?php

namespace App\Filament\Resources\Forms\Tables;

use App\Enums\FormStatus;
use App\Models\Form;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Js;
use Illuminate\Support\Str;

class FormsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('formType.name')
                    ->label('Form type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->counts('submissions')
                    ->sortable(),
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
                self::duplicateAction(),
                self::openAction(),
                self::closeAction(),
                Action::make('openSwagger')
                    ->label('Open Swagger')
                    ->icon(Heroicon::ArrowTopRightOnSquare)
                    ->url(fn (Form $record): string => url("/forms/{$record->uuid}/swagger"))
                    ->openUrlInNewTab(),
                self::copyUrlAction(
                    name: 'copyEndpoint',
                    label: 'Copy endpoint',
                    url: fn (Form $record): string => url("/api/forms/{$record->uuid}/submissions"),
                ),
                self::copyUrlAction(
                    name: 'copyOpenApiUrl',
                    label: 'Copy OpenAPI URL',
                    url: fn (Form $record): string => url("/api/forms/{$record->uuid}/openapi.json"),
                ),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    private static function duplicateAction(): Action
    {
        return Action::make('duplicate')
            ->icon(Heroicon::DocumentDuplicate)
            ->visible(fn (): bool => self::isAdmin())
            ->action(function (Form $record): void {
                $duplicate = $record->replicate();
                $duplicate->uuid = (string) Str::uuid();
                $duplicate->name = "{$record->name} (copia)";
                $duplicate->save();

                $duplicate->users()->sync($record->users()->pluck('users.id')->all());

                Notification::make()
                    ->title('Form duplicato')
                    ->success()
                    ->send();
            });
    }

    private static function openAction(): Action
    {
        return Action::make('open')
            ->icon(Heroicon::LockOpen)
            ->visible(fn (Form $record): bool => self::isAdmin() && $record->status === FormStatus::Closed)
            ->action(fn (Form $record): bool => $record->update(['status' => FormStatus::Open]));
    }

    private static function closeAction(): Action
    {
        return Action::make('close')
            ->icon(Heroicon::LockClosed)
            ->visible(fn (Form $record): bool => self::isAdmin() && $record->status === FormStatus::Open)
            ->action(fn (Form $record): bool => $record->update(['status' => FormStatus::Closed]));
    }

    /**
     * @param  callable(Form): string  $url
     */
    private static function copyUrlAction(string $name, string $label, callable $url): Action
    {
        return Action::make($name)
            ->label($label)
            ->icon(Heroicon::ClipboardDocument)
            ->alpineClickHandler(fn (Form $record): string => 'window.navigator.clipboard.writeText('.Js::from($url($record)).')');
    }

    private static function isAdmin(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->is_admin;
    }
}
