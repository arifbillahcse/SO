<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageSeoResource\Pages;
use App\Models\PageSeo;
use App\Services\SeoAnalyzer;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PageSeoResource extends Resource
{
    protected static ?string $model = PageSeo::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static string | \UnitEnum | null $navigationGroup = 'SEO';

    protected static ?string $navigationLabel = 'Page SEO';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('route_name')->label('Page')->disabled(),

            TextInput::make('title')->label('SEO Title')->maxLength(255)
                ->helperText('Overrides the default page title. Leave blank to use the default.'),
            Textarea::make('meta_description')->rows(3)->maxLength(320)->columnSpanFull()
                ->helperText('Shown under your title in Google search results.'),
            TextInput::make('focus_keywords')
                ->label('Focus Keywords')
                ->helperText('Comma-separated, up to 5, e.g. "web development, laravel agency"')
                ->afterStateHydrated(fn ($component, $state) => $component->state(is_array($state) ? implode(', ', $state) : $state))
                ->dehydrateStateUsing(fn ($state) => array_values(array_filter(array_map('trim', explode(',', $state ?? ''))))),

            Select::make('schema_type')->label('Schema.org Type')->options([
                'WebPage' => 'Web Page (generic)',
                'Service' => 'Service',
                'AboutPage' => 'About Page',
            ])->default('WebPage')->required(),

            TextInput::make('canonical_url')->url()->maxLength(255)
                ->helperText('Leave blank to use this page\'s own URL.'),
            Toggle::make('robots_index')->label('Allow search engines to index this page')->default(true),
            Toggle::make('robots_follow')->label('Allow search engines to follow links on this page')->default(true),
            TextInput::make('robots_advanced')->label('Advanced robots directives')
                ->helperText('Comma-separated, e.g. "noarchive, nosnippet"'),

            TextInput::make('og_title')->label('Open Graph Title')->maxLength(255)
                ->helperText('Leave blank to reuse the SEO title.'),
            Textarea::make('og_description')->label('Open Graph Description')->rows(3)->maxLength(320)->columnSpanFull(),
            FileUpload::make('og_image')->label('Open Graph Image')->image()->directory('seo')->visibility('public'),
            Select::make('twitter_card_type')->label('Twitter Card Type')->options([
                'summary' => 'Summary',
                'summary_large_image' => 'Summary with Large Image',
            ])->default('summary_large_image')->required(),
            TextInput::make('twitter_title')->label('Twitter Title')->maxLength(255)
                ->helperText('Leave blank to reuse the SEO title.'),
            Textarea::make('twitter_description')->label('Twitter Description')->rows(3)->maxLength(320)->columnSpanFull(),
            FileUpload::make('twitter_image')->label('Twitter Image')->image()->directory('seo')->visibility('public'),

            Placeholder::make('seo_analysis')
                ->label('SEO Analysis')
                ->columnSpanFull()
                ->content(fn (?PageSeo $record) => $record ? static::renderSeoAnalysis($record) : 'Save the page first to run an analysis.'),
        ]);
    }

    protected static function renderSeoAnalysis(PageSeo $record): HtmlString
    {
        try {
            $url = route($record->route_name);
        } catch (\Throwable) {
            return new HtmlString('<p class="text-sm text-gray-500">Unable to resolve this page\'s URL.</p>');
        }

        $checks = app(SeoAnalyzer::class)->analyze($record, $url);

        $items = collect($checks)->map(function (array $check) {
            $icon = $check['passed']
                ? '<span style="color:#16a34a;">&#10003;</span>'
                : '<span style="color:#dc2626;">&#10007;</span>';
            $message = $check['passed'] ? '' : '<div style="font-size:12px;color:#6b7280;margin-top:2px;">'.e($check['message']).'</div>';

            return '<li style="padding:6px 0;border-bottom:1px solid #e5e7eb;">'.$icon.' <strong>'.e($check['label']).'</strong>'.$message.'</li>';
        })->implode('');

        return new HtmlString('<ul style="list-style:none;margin:0;padding:0;">'.$items.'</ul>');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('route_name')->label('Page')->searchable(),
                TextColumn::make('title')->label('SEO Title')->limit(50)->placeholder('— using default —'),
                IconColumn::make('robots_index')->label('Indexed')->boolean(),
            ])
            ->defaultSort('route_name')
            ->recordActions([
                EditAction::make(),
            ]);
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
