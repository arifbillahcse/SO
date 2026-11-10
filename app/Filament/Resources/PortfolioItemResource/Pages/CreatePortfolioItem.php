<?php

namespace App\Filament\Resources\PortfolioItemResource\Pages;

use App\Filament\Concerns\RescansSeo;
use App\Filament\Resources\PortfolioItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePortfolioItem extends CreateRecord
{
    use RescansSeo;

    protected static string $resource = PortfolioItemResource::class;
}
