<?php

namespace App\Filament\Resources\Submissions\Tables;

use App\Enums\NotificationStatus;
use App\Enums\SubmissionStatus;
use App\Filament\Resources\Submissions\SubmissionResource;
use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('form.name')
                    ->label('Form')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('submission_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('notification_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('email')
                    ->state(fn (Submission $record): ?string => self::fieldValue($record, 'email'))
                    ->searchable(false),
                TextColumn::make('display_name')
                    ->label('Name')
                    ->state(fn (Submission $record): ?string => self::displayName($record))
                    ->searchable(false),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('form_id')
                    ->label('Form')
                    ->options(SubmissionResource::accessibleFormOptions()),
                SelectFilter::make('submission_status')
                    ->options(SubmissionStatus::class),
                SelectFilter::make('notification_status')
                    ->options(NotificationStatus::class),
                Filter::make('submitted_at')
                    ->schema([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => self::applySubmittedAtFilter($query, $data)),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                self::markAction(SubmissionStatus::InProgress),
                self::markAction(SubmissionStatus::Handled),
                self::markAction(SubmissionStatus::Discarded),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    self::markBulkAction(SubmissionStatus::InProgress),
                    self::markBulkAction(SubmissionStatus::Handled),
                    self::markBulkAction(SubmissionStatus::Discarded),
                ]),
            ])
            ->defaultSort('submitted_at', 'desc');
    }

    private static function markAction(SubmissionStatus $status): Action
    {
        return Action::make("mark_{$status->value}")
            ->label($status->getLabel())
            ->icon(self::iconFor($status))
            ->action(fn (Submission $record): bool => $record->update(['submission_status' => $status]))
            ->visible(fn (Submission $record): bool => $record->submission_status !== $status);
    }

    private static function markBulkAction(SubmissionStatus $status): BulkAction
    {
        return BulkAction::make("bulk_mark_{$status->value}")
            ->label($status->getLabel())
            ->icon(self::iconFor($status))
            ->action(function (Collection $records) use ($status): void {
                self::markRecords($records, $status);
            })
            ->deselectRecordsAfterCompletion();
    }

    private static function iconFor(SubmissionStatus $status): Heroicon
    {
        return match ($status) {
            SubmissionStatus::InProgress => Heroicon::ArrowPath,
            SubmissionStatus::Handled => Heroicon::CheckCircle,
            SubmissionStatus::Discarded => Heroicon::XMark,
            default => Heroicon::Pencil,
        };
    }

    private static function displayName(Submission $submission): ?string
    {
        foreach (['full_name', 'contact_name', 'company_name', 'organization_name'] as $field) {
            $value = self::fieldValue($submission, $field);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    private static function fieldValue(Submission $submission, string $field): ?string
    {
        $value = $submission->fields[$field]['value'] ?? null;

        return is_scalar($value) ? (string) $value : null;
    }

    /**
     * @param  Builder<Submission>  $query
     * @param  array<array-key, mixed>  $data
     * @return Builder<Submission>
     */
    private static function applySubmittedAtFilter(Builder $query, array $data): Builder
    {
        return $query
            ->when(self::filterDate($data, 'from'), fn (Builder $query, string $date): Builder => $query->whereDate('submitted_at', '>=', $date))
            ->when(self::filterDate($data, 'until'), fn (Builder $query, string $date): Builder => $query->whereDate('submitted_at', '<=', $date));
    }

    /**
     * @param  Collection<int, Submission>  $records
     */
    private static function markRecords(Collection $records, SubmissionStatus $status): void
    {
        $records->each(fn (Submission $submission): bool => $submission->update(['submission_status' => $status]));
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private static function filterDate(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }
}
