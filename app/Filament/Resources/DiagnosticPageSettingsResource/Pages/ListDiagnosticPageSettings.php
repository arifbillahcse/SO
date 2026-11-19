<?php

namespace App\Filament\Resources\DiagnosticPageSettingsResource\Pages;

use App\Filament\Resources\DiagnosticPageSettingsResource;
use App\Models\DiagnosticPageSettings;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListDiagnosticPageSettings extends ListRecords
{
    protected static string $resource = DiagnosticPageSettingsResource::class;

    public function mount(): void
    {
        DiagnosticPageSettings::current();
        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [Action::make('view')->label('View DiagnoSolution plans')->url(fn (): string => route('diagnostic-management-system').'#plans')->openUrlInNewTab()];
    }
}
