<?php

namespace App\Filament\Resources\FormTypes\Pages;

use App\Filament\Resources\FormTypes\FormTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFormType extends ViewRecord
{
    protected static string $resource = FormTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
