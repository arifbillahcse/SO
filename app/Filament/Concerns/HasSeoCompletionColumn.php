<?php

namespace App\Filament\Concerns;

use Filament\Tables\Columns\TextColumn;

trait HasSeoCompletionColumn
{
    protected static function seoCompletionColumn(string $titleField = 'meta_title'): TextColumn
    {
        return TextColumn::make('seo_completion')
            ->label('SEO')
            ->state(function ($record) use ($titleField): string {
                $filled = collect([
                    filled($record->{$titleField}),
                    filled($record->meta_description),
                    filled($record->focus_keywords),
                ])->filter()->count();

                return match ($filled) {
                    3 => 'Complete',
                    0 => 'Missing',
                    default => 'Partial',
                };
            })
            ->badge()
            ->color(fn (string $state): string => match ($state) {
                'Complete' => 'success',
                'Partial' => 'warning',
                default => 'danger',
            });
    }
}
