<?php

namespace App\Filament\Seo;

use App\Models\PageSeo;
use App\Services\Seo\SearchPerformance;
use App\Support\Seo;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * The SEO editing UI, shared by the Page SEO screen and every content form
 * (articles, case studies, portfolio, services) so there is one set of fields.
 */
class SeoFormSchema
{
    public static function tabs(): Tabs
    {
        return Tabs::make('SEO')
            ->persistTabInQueryString('seo-tab')
            ->columnSpanFull()
            ->tabs([
                Tab::make('Search appearance')->icon('heroicon-o-magnifying-glass')->schema(static::basic()),
                Tab::make('Indexing')->icon('heroicon-o-shield-check')->schema(static::indexing()),
                Tab::make('Social sharing')->icon('heroicon-o-share')->schema(static::social()),
                Tab::make('Structured data')->icon('heroicon-o-code-bracket')->schema(static::structuredData()),
                Tab::make('SEO analysis')->icon('heroicon-o-clipboard-document-check')->schema(static::analysis()),
            ]);
    }

    /**
     * @return array<Component>
     */
    public static function basic(): array
    {
        return [
            Grid::make(['default' => 1, 'lg' => 5])->schema([
                Grid::make(1)->columnSpan(['lg' => 3])->schema([
                    TextInput::make('title')
                        ->label('SEO title')
                        ->maxLength(70)
                        ->live(onBlur: true)
                        ->helperText(fn (?string $state) => static::counter($state, 50, 60, 'Shown as the blue link in Google. Aim for 50–60 characters.')),
                    Textarea::make('meta_description')
                        ->label('Meta description')
                        ->rows(3)
                        ->maxLength(320)
                        ->live(onBlur: true)
                        ->helperText(fn (?string $state) => static::counter($state, 140, 160, 'The snippet under the title. Aim for 140–160 characters.')),
                    Grid::make(2)->schema([
                        TextInput::make('focus_keyword')
                            ->label('Primary focus keyword')
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->helperText('The main phrase this page should rank for. Editorial only — not output as a meta tag.'),
                        TagsInput::make('secondary_keywords')
                            ->label('Secondary keywords')
                            ->placeholder('Add a phrase and press Enter')
                            ->helperText('Related phrases covered on the page.'),
                    ]),
                    Textarea::make('content_notes')
                        ->label('SEO content notes')
                        ->rows(2)
                        ->maxLength(2000)
                        ->helperText('Internal notes: search intent, content gaps, ideas. Never shown on the site.'),
                ]),
                Html::make(fn (Get $get, ?PageSeo $record) => static::googlePreview($get, $record))
                    ->columnSpan(['lg' => 2]),
            ]),
        ];
    }

    /**
     * @return array<Component>
     */
    public static function indexing(): array
    {
        return [
            Html::make(fn (?PageSeo $record) => static::noindexNotice($record)),
            Grid::make(2)->schema([
                Toggle::make('robots_index')
                    ->label('Allow search engines to index this page (index)')
                    ->default(true)
                    ->live(),
                Toggle::make('robots_follow')
                    ->label('Allow search engines to follow its links (follow)')
                    ->default(true),
            ]),
            TextInput::make('canonical_url')
                ->label('Canonical URL')
                ->url()
                ->maxLength(255)
                ->placeholder(fn (?PageSeo $record) => $record?->selfUrl() ?? 'This page\'s own URL')
                ->helperText('Leave blank (recommended) to point to this page itself. Only set it when another URL holds the same content.')
                ->rules([
                    fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                        if (blank($value)) {
                            return;
                        }
                        if (! str_starts_with($value, 'https://')) {
                            $fail('Use a full https:// URL.');
                        }
                        if (! $get('robots_index')) {
                            $fail('A noindex page should not also set a canonical to another URL — choose one or the other.');
                        }
                    },
                ]),
            Select::make('robots_advanced')
                ->label('Advanced robots directives')
                ->multiple()
                ->options(config('seo.robots_directives'))
                ->afterStateHydrated(fn (Select $component, $state) => $component->state(
                    is_string($state) ? array_values(array_filter(array_map('trim', explode(',', $state)))) : ($state ?? [])
                ))
                ->dehydrateStateUsing(fn ($state) => $state ? implode(', ', $state) : null)
                ->helperText('Optional. Most pages need none of these.'),
        ];
    }

    /**
     * @return array<Component>
     */
    public static function social(): array
    {
        return [
            Grid::make(['default' => 1, 'lg' => 5])->schema([
                Grid::make(1)->columnSpan(['lg' => 3])->schema([
                    TextInput::make('og_title')
                        ->label('Open Graph title')
                        ->maxLength(95)
                        ->live(onBlur: true)
                        ->helperText('Facebook, LinkedIn, WhatsApp. Leave blank to reuse the SEO title.'),
                    Textarea::make('og_description')
                        ->label('Open Graph description')
                        ->rows(2)
                        ->maxLength(320)
                        ->live(onBlur: true)
                        ->helperText('Leave blank to reuse the meta description.'),
                    FileUpload::make('og_image')
                        ->label('Open Graph image')
                        ->image()
                        ->directory('seo')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->live()
                        ->helperText('1200 × 630 px recommended. Leave empty to use the default Softorio share image.'),
                    Select::make('twitter_card_type')
                        ->label('X (Twitter) card type')
                        ->options([
                            'summary_large_image' => 'Summary with large image',
                            'summary' => 'Summary (small square image)',
                        ])
                        ->default('summary_large_image')
                        ->selectablePlaceholder(false)
                        ->live(),
                    TextInput::make('twitter_title')
                        ->label('X (Twitter) title')
                        ->maxLength(70)
                        ->live(onBlur: true)
                        ->helperText('Leave blank to reuse the Open Graph title.'),
                    Textarea::make('twitter_description')
                        ->label('X (Twitter) description')
                        ->rows(2)
                        ->maxLength(200)
                        ->live(onBlur: true),
                    FileUpload::make('twitter_image')
                        ->label('X (Twitter) image')
                        ->image()
                        ->directory('seo')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->live()
                        ->helperText('Leave empty to reuse the Open Graph image.'),
                ]),
                Html::make(fn (Get $get, ?PageSeo $record) => static::socialPreview($get, $record))
                    ->columnSpan(['lg' => 2]),
            ]),
        ];
    }

    /**
     * @return array<Component>
     */
    public static function structuredData(): array
    {
        return [
            Select::make('schema_type')
                ->label('Schema.org type')
                ->options(fn (?PageSeo $record) => static::schemaOptions($record))
                ->default('WebPage')
                ->selectablePlaceholder(false)
                ->disabled(fn (?PageSeo $record) => $record?->seoable_type !== null)
                ->helperText(fn (?PageSeo $record) => $record?->seoable_type
                    ? 'Generated automatically from this item\'s content (Article / CreativeWork), with breadcrumbs.'
                    : 'Describes what the page is. Organization and BreadcrumbList are added automatically.'),
            Html::make(fn (?PageSeo $record) => static::view('jsonld', ['record' => $record])),
        ];
    }

    /**
     * @return array<Component>
     */
    public static function analysis(): array
    {
        return [
            Html::make(fn (?PageSeo $record) => static::view('analysis', [
                'record' => $record,
                'searchConnected' => app(SearchPerformance::class)->isConnected(),
            ])),
        ];
    }

    public static function scoreColumn(string $name = 'seo_score', bool $sortable = true): TextColumn
    {
        return TextColumn::make($name)
            ->label('SEO score')
            ->badge()
            ->formatStateUsing(fn ($state) => $state === null ? 'Not scanned' : "{$state} / 100")
            ->color(fn ($state) => match (true) {
                $state === null => 'gray',
                $state >= PageSeo::SCORE_GOOD => 'success',
                $state >= PageSeo::SCORE_WARNING => 'warning',
                default => 'danger',
            })
            ->placeholder('Not scanned')
            ->sortable($sortable);
    }

    private static function schemaOptions(?PageSeo $record): array
    {
        $all = config('seo.schema_types');

        return match ($record?->page_type) {
            'article' => array_intersect_key($all, ['Article' => 1]),
            'case_study', 'portfolio' => array_intersect_key($all, ['CreativeWork' => 1]),
            default => array_diff_key($all, ['Article' => 1, 'CreativeWork' => 1]),
        };
    }

    private static function counter(?string $state, int $min, int $max, string $hint): HtmlString
    {
        $length = mb_strlen((string) $state);
        $color = match (true) {
            $length === 0 => '#6b7280',
            $length >= $min && $length <= $max => '#16a34a',
            default => '#d97706',
        };

        return new HtmlString('<span style="color:'.$color.';font-weight:600">'.$length.' characters</span> · '.e($hint));
    }

    private static function noindexNotice(?PageSeo $record): ?HtmlString
    {
        if (! $record || ! $record->seoable || ! ($reason = $record->seoable->seoNoindexReason())) {
            return null;
        }

        return new HtmlString('<div style="padding:12px 14px;border-radius:8px;background:#fffbeb;border:1px solid #fcd34d;color:#92400e;font-size:13px"><strong>Served as noindex regardless of the toggle below:</strong> '.e($reason).'</div>');
    }

    private static function googlePreview(Get $get, ?PageSeo $record): HtmlString
    {
        $page = $record?->seo_checks['page'] ?? [];
        $default = static::defaults($record);

        return static::view('google-preview', [
            'url' => $record?->selfUrl() ?? Seo::siteUrl().'/',
            'title' => $get('title') ?: ($default['title'] ?? $page['title'] ?? 'Page title'),
            'description' => $get('meta_description') ?: ($default['description'] ?? $page['description'] ?? ''),
            'usingDefaultTitle' => blank($get('title')),
            'usingDefaultDescription' => blank($get('meta_description')),
            'keyword' => $get('focus_keyword'),
        ]);
    }

    private static function socialPreview(Get $get, ?PageSeo $record): HtmlString
    {
        $page = $record?->seo_checks['page'] ?? [];
        $default = static::defaults($record);
        $title = $get('og_title') ?: $get('title') ?: ($default['title'] ?? $page['title'] ?? 'Page title');
        $description = $get('og_description') ?: $get('meta_description') ?: ($default['description'] ?? $page['description'] ?? '');
        $ogImage = static::imagePreviewUrl($get('og_image')) ?: Seo::imageUrl($record?->seoable?->seoDefaultImage()) ?: asset(config('seo.default_og_image'));

        return static::view('social-preview', [
            'domain' => parse_url(Seo::siteUrl(), PHP_URL_HOST),
            'title' => $title,
            'description' => $description,
            'image' => $ogImage,
            'twitterTitle' => $get('twitter_title') ?: $title,
            'twitterDescription' => $get('twitter_description') ?: $description,
            'twitterImage' => static::imagePreviewUrl($get('twitter_image')) ?: $ogImage,
            'twitterCard' => $get('twitter_card_type') ?: 'summary_large_image',
        ]);
    }

    /**
     * What the page shows when its SEO fields are left blank.
     *
     * @return array{title?: ?string, description?: ?string}
     */
    private static function defaults(?PageSeo $record): array
    {
        if ($record?->seoable) {
            return [
                'title' => $record->seoable->seoDefaultTitle(),
                'description' => Seo::excerpt($record->seoable->seoDefaultDescription()),
            ];
        }

        return [];
    }

    private static function imagePreviewUrl(mixed $state): ?string
    {
        $file = is_array($state) ? (array_values($state)[0] ?? null) : $state;

        try {
            return match (true) {
                $file instanceof TemporaryUploadedFile => $file->temporaryUrl(),
                is_string($file) && $file !== '' => Storage::disk('public')->url($file),
                default => null,
            };
        } catch (\Throwable) {
            return null;
        }
    }

    private static function view(string $name, array $data): HtmlString
    {
        return new HtmlString(view("filament.seo.{$name}", $data)->render());
    }
}
