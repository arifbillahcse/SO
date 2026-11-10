<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageSeoResource\Pages;
use App\Filament\Seo\SeoFormSchema;
use App\Models\PageSeo;
use App\Services\Seo\SearchPerformance;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PageSeoResource extends Resource
{
    protected static ?string $model = PageSeo::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static string|\UnitEnum|null $navigationGroup = 'SEO';

    protected static ?string $navigationLabel = 'Page SEO';

    protected static ?string $modelLabel = 'page SEO';

    protected static ?string $pluralModelLabel = 'Page SEO';

    protected static ?int $navigationSort = -10;

    protected static ?string $recordTitleAttribute = 'page_label';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            SeoFormSchema::tabs(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $search = app(SearchPerformance::class);

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('seoable'))
            ->columns([
                TextColumn::make('page_label')
                    ->label('Page')
                    ->weight('medium')
                    ->description(fn (PageSeo $record) => $record->path().' · '.$record->typeLabel())
                    ->searchable(['page_label', 'page_path', 'route_name'])
                    ->sortable()
                    ->wrap(),
                TextColumn::make('title')
                    ->label('Title in Google')
                    ->state(fn (PageSeo $record) => $record->title ?: ($record->seo_checks['page']['title'] ?? null))
                    ->description(fn (PageSeo $record) => blank($record->title) ? 'Template default — not set' : null)
                    ->color(fn (PageSeo $record) => blank($record->title) ? 'gray' : null)
                    ->placeholder('Not set')
                    ->searchable()
                    ->wrap()
                    ->width('22rem')
                    ->limit(70),
                TextColumn::make('meta_description')
                    ->label('Description')
                    ->state(fn (PageSeo $record) => filled($record->meta_description) ? mb_strlen($record->meta_description).' characters' : 'Missing')
                    ->tooltip(fn (PageSeo $record) => $record->meta_description)
                    ->badge()
                    ->color(fn (PageSeo $record) => match (true) {
                        blank($record->meta_description) => 'danger',
                        mb_strlen($record->meta_description) >= 120 && mb_strlen($record->meta_description) <= 160 => 'success',
                        default => 'warning',
                    }),
                SeoFormSchema::scoreColumn(),
                TextColumn::make('google')
                    ->label('Google, 28 days')
                    ->state(function (PageSeo $record) use ($search) {
                        $metrics = $search->forUrl($record->selfUrl());

                        return $metrics
                            ? "{$metrics['clicks']} clicks · position ".number_format($metrics['position'], 1)
                            : ($search->isConnected() ? 'No data' : 'Not connected');
                    })
                    ->color('gray')
                    ->tooltip($search->isConnected() ? null : 'Google Search Console is not connected, so no clicks or positions are shown.'),
                TextColumn::make('focus_keyword')
                    ->label('Focus keyword')
                    ->placeholder('—')
                    ->searchable()
                    ->wrap(),
                IconColumn::make('indexable')
                    ->label('Robots')
                    ->state(fn (PageSeo $record) => $record->isIndexable())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->falseColor('gray')
                    ->tooltip(fn (PageSeo $record) => $record->isIndexable() ? 'index — in the sitemap' : 'noindex — '.$record->noindexReason()),
                TextColumn::make('google_index')
                    ->label('Indexed')
                    ->state(fn (PageSeo $record) => match ($search->isIndexed($record->selfUrl())) {
                        true => 'Indexed',
                        false => 'Not indexed',
                        null => 'Not checked',
                    })
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Indexed' => 'success',
                        'Not indexed' => 'danger',
                        default => 'gray',
                    })
                    ->tooltip('Verified Google index status needs Search Console. Use URL Inspection in Search Console to check a page.'),
            ])
            ->defaultSort(fn (Builder $query) => $query
                ->orderByRaw('route_name is null')
                ->orderByRaw("case page_type when 'page' then 1 when 'service' then 2 when 'solution' then 3 when 'listing' then 4 when 'article' then 5 when 'case_study' then 6 else 7 end")
                ->orderBy('id'))
            ->filters([
                SelectFilter::make('page_type')
                    ->label('Content type')
                    ->options(config('seo.page_types')),
                SelectFilter::make('score')
                    ->label('SEO score')
                    ->options([
                        'good' => 'Good (80–100)',
                        'warning' => 'Needs improvement (50–79)',
                        'danger' => 'Incomplete (0–49)',
                        'unscanned' => 'Not scanned',
                    ])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'good' => $query->where('seo_score', '>=', PageSeo::SCORE_GOOD),
                        'warning' => $query->whereBetween('seo_score', [PageSeo::SCORE_WARNING, PageSeo::SCORE_GOOD - 1]),
                        'danger' => $query->where('seo_score', '<', PageSeo::SCORE_WARNING),
                        'unscanned' => $query->whereNull('seo_score'),
                        default => $query,
                    }),
                SelectFilter::make('missing')
                    ->label('Missing information')
                    ->options([
                        'title' => 'Missing SEO title',
                        'meta_description' => 'Missing meta description',
                        'focus_keyword' => 'Missing focus keyword',
                    ])
                    ->query(fn (Builder $query, array $data) => filled($data['value'] ?? null)
                        ? $query->where(fn (Builder $q) => $q->whereNull($data['value'])->orWhere($data['value'], ''))
                        : $query),
                TernaryFilter::make('robots_index')
                    ->label('Robots setting')
                    ->trueLabel('index')
                    ->falseLabel('noindex'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('view')
                    ->label('View page')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconButton()
                    ->color('gray')
                    ->url(fn (PageSeo $record) => url($record->path()))
                    ->openUrlInNewTab(),
            ])
            ->recordUrl(fn (PageSeo $record) => static::getUrl('edit', ['record' => $record]))
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(50);
    }

    public static function getRecordTitle(?Model $record): string
    {
        return $record ? Str::limit($record->page_label ?? 'Page', 60) : 'Page SEO';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPageSeos::route('/'),
            'edit' => Pages\EditPageSeo::route('/{record}/edit'),
        ];
    }
}
