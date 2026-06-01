<?php

namespace App\Filament\Resources\FormTypes\Pages;

use App\Filament\Resources\FormTypes\FormTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFormTypes extends ListRecords
{
    protected static string $resource = FormTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
