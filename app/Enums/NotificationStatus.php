<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NotificationStatus: string implements HasLabel
{
    case NotRequired = 'not_required';
    case Sent = 'sent';
    case Failed = 'failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::NotRequired => 'Not required',
            self::Sent => 'Sent',
            self::Failed => 'Failed',
        };
    }
}
