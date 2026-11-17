<?php

namespace App\Filament\Resources\ClientBrandResource\Pages;

use App\Filament\Resources\ClientBrandResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientBrands extends ListRecords
{
    protected static string $resource = ClientBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
