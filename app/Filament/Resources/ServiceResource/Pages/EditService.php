<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Concerns\RescansSeo;
use App\Filament\Resources\ServiceResource;
use App\Models\Service;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    use RescansSeo;

    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->modalDescription(fn (Service $record): string => 'Deleting this core service also deletes its '.$record->offerings()->count().' detailed offerings. Unpublish the group instead if you want to keep its content.'),
        ];
    }
}
