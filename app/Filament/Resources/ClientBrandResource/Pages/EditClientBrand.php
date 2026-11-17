<?php

namespace App\Filament\Resources\ClientBrandResource\Pages;

use App\Filament\Resources\ClientBrandResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClientBrand extends EditRecord
{
    protected static string $resource = ClientBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
