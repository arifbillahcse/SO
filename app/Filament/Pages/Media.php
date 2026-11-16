<?php

namespace App\Filament\Pages;

use App\Services\MediaLibrary;
use Filament\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class Media extends Page
{
    use WithPagination;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Media';

    protected string $view = 'filament.pages.media';

    public string $search = '';

    public string $source = 'all';

    public string $usage = 'all';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'source', 'usage'], true)) {
            $this->resetPage();
        }
    }

    protected function getViewData(): array
    {
        $library = app(MediaLibrary::class)->images();
        $filtered = $library->filter(function (array $image): bool {
            $matchesSource = $this->source === 'all' || $image['source'] === $this->source;
            $matchesUsage = match ($this->usage) {
                'referenced' => count($image['uses']) > 0,
                'unreferenced' => count($image['uses']) === 0,
                'missing' => $image['exists'] === false,
                default => true,
            };
            $haystack = $image['name'].' '.$image['path'].' '.implode(' ', array_column($image['uses'], 'label'));

            return $matchesSource && $matchesUsage && (blank(trim($this->search)) || Str::contains($haystack, trim($this->search), ignoreCase: true));
        })->values();
        $page = $this->getPage();

        return [
            'images' => new LengthAwarePaginator($filtered->forPage($page, 24)->values(), $filtered->count(), 24, $page),
            'totalImages' => $library->count(),
            'referencedImages' => $library->filter(fn (array $image): bool => count($image['uses']) > 0)->count(),
            'missingImages' => $library->where('exists', false)->where('source', '!=', 'external')->count(),
        ];
    }
}
