<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FieldType: string implements HasLabel
{
    case String = 'string';
    case Text = 'text';
    case Email = 'email';
    case Url = 'url';
    case Number = 'number';
    case Boolean = 'boolean';

    public function getLabel(): string
    {
        return match ($this) {
            self::String => 'String',
            self::Text => 'Text',
            self::Email => 'Email',
            self::Url => 'URL',
            self::Number => 'Number',
            self::Boolean => 'Boolean',
        };
    }
}
