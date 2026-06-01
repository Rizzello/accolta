<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SubmissionStatus: string implements HasLabel
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Handled = 'handled';
    case Discarded = 'discarded';

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'New',
            self::InProgress => 'In progress',
            self::Handled => 'Handled',
            self::Discarded => 'Discarded',
        };
    }
}
