<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Concerns\RescansSeo;
use App\Filament\Resources\ArticleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    use RescansSeo;

    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
