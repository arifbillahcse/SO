<?php

namespace App\Filament\Resources\CaseStudyResource\Pages;

use App\Filament\Concerns\RescansSeo;
use App\Filament\Resources\CaseStudyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCaseStudy extends CreateRecord
{
    use RescansSeo;

    protected static string $resource = CaseStudyResource::class;
}
