<?php

namespace App\Filament\Resources\FormTypes\Pages;

use App\Filament\Resources\FormTypes\FormTypeResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditFormType extends EditRecord
{
    protected static string $resource = FormTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            FormTypeResource::protectedDeleteAction(),
        ];
    }
}
