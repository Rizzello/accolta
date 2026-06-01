<?php

namespace App\Filament\Resources\Submissions\Schemas;

use App\Enums\SubmissionStatus;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class SubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('submission_status')
                    ->required()
                    ->options(SubmissionStatus::class),
            ]);
    }
}
