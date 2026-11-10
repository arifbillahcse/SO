<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Concerns\RescansSeo;
use App\Filament\Resources\ServiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    use RescansSeo;

    protected static string $resource = ServiceResource::class;
}
