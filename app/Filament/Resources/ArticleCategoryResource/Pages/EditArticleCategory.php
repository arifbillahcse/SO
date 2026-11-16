<?php

namespace App\Filament\Resources\ArticleCategoryResource\Pages;

use App\Filament\Resources\ArticleCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArticleCategory extends EditRecord
{
    protected static string $resource = ArticleCategoryResource::class;

    public function getSubheading(): ?string
    {
        return $this->record->articles()->exists()
            ? 'This category is in use. Reassign its articles before deleting it. Renaming updates public labels automatically.'
            : null;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => ArticleCategoryResource::canDelete($this->getRecord()))
                ->authorize(fn (): bool => ArticleCategoryResource::canDelete($this->getRecord())),
        ];
    }
}
