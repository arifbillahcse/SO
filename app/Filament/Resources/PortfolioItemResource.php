<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PortfolioItemResource\Pages;
use App\Filament\Seo\SeoFormSchema;
use App\Models\PortfolioItem;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PortfolioItemResource extends Resource
{
    protected static ?string $model = PortfolioItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Portfolio';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255)->live(onBlur: true)
                ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
            TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            Select::make('project_type')->options(PortfolioItem::PROJECT_TYPE_OPTIONS)
                ->default('website_project')->required()->live(),
            TextInput::make('client')->maxLength(255)
                ->required(fn (Get $get): bool => $get('project_type') !== 'technical_demonstration')
                ->helperText('Leave blank for a technical demonstration without a named client.'),
            TextInput::make('attribution')->label('Author / contributor')->maxLength(255)
                ->required(fn (Get $get): bool => $get('project_type') === 'technical_demonstration')
                ->helperText('Credit the person who created or demonstrated this work.'),
            TextInput::make('website_url')->label('Website URL')->url()
                ->rules(['url:http,https'])->maxLength(2048)
                ->helperText('Public project or demo address, including https://. Leave blank if unavailable.'),
            Select::make('category')->options(PortfolioItem::CATEGORY_OPTIONS)->required(),
            TextInput::make('tags')->label('Technologies and focus tags')->required()->helperText('Comma-separated technologies or areas of work, e.g. WordPress, WooCommerce, PHP.')
                ->afterStateHydrated(fn ($component, $state) => $component->state(is_array($state) ? implode(', ', $state) : $state))
                ->dehydrateStateUsing(fn ($state) => array_values(array_filter(array_map('trim', explode(',', $state))))),
            FileUpload::make('thumbnail_path')->label('Project image')->image()->disk('public')->directory('portfolio')->visibility('public')
                ->helperText('Optional screenshot or project image. Without one, the website displays a title cover automatically.'),
            Textarea::make('description')->label('Project brief')->rows(3)->columnSpanFull()
                ->helperText('A short, factual summary shown on the project card and at the top of its detail page. Explain what the project covers.'),
            RichEditor::make('body')->label('Project write-up')->columnSpanFull()
                ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('portfolio/details')->fileAttachmentsVisibility('public')
                ->helperText('Add Overview and What we did headings, the work completed, and supporting screenshots. Only include outcomes you can verify. Leave blank to show the brief and project information.'),
            TextInput::make('sort_order')->numeric()->default(0),

            Section::make('Demonstration evidence')
                ->description('Record what the supporting links establish. A matching video title does not verify a customer engagement or a complete project.')
                ->schema([
                    TextInput::make('video_url')->label('Video URL')->url()
                        ->rules(['url:http,https'])->maxLength(2048)
                        ->required(fn (Get $get): bool => in_array($get('evidence_status'), ['available_video_title_matches_topic', 'available_video_partially_matches_topic'], true))
                        ->helperText('YouTube or another public demonstration video. Keep unavailable source links here for reference.'),
                    TextInput::make('repository_url')->label('Repository URL')->url()
                        ->rules(['url:http,https'])->maxLength(2048)
                        ->required(fn (Get $get): bool => $get('evidence_status') === 'repository_available')
                        ->helperText('Link to the specific project repository rather than a general profile.'),
                    Select::make('evidence_status')->options(PortfolioItem::EVIDENCE_STATUS_OPTIONS)
                        ->default('not_reviewed')->required()->live()
                        ->helperText('Only a matching available video or reviewed repository is eligible for a public evidence button.'),
                    DateTimePicker::make('evidence_checked_at')->label('Evidence reviewed at')->seconds(false),
                    Textarea::make('evidence_notes')->rows(3)->columnSpanFull()
                        ->helperText('Record shared videos, partial matches, unavailable links, or other limits. Internal review notes.'),
                    TextInput::make('source_key')->label('Import reference')->disabled()->dehydrated(false)
                        ->helperText('Assigned by the importer so repeated evidence links do not merge separate topics.'),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('SEO')
                ->description('How this page appears in Google and when shared. Saved values go live immediately.')
                ->relationship('seo')
                ->schema([SeoFormSchema::tabs('portfolio')])
                ->collapsible()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->sortable()->label('#'),
                TextColumn::make('title')->searchable(),
                TextColumn::make('client'),
                TextColumn::make('category')->badge(),
                TextColumn::make('project_type')->label('Type')->badge()
                    ->formatStateUsing(fn (string $state): string => PortfolioItem::PROJECT_TYPE_OPTIONS[$state] ?? $state),
                TextColumn::make('evidence_status')->label('Evidence')
                    ->formatStateUsing(fn (string $state): string => PortfolioItem::EVIDENCE_STATUS_OPTIONS[$state] ?? $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                SeoFormSchema::scoreColumn('seo.seo_score', sortable: false)->label('SEO'),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('category')->options(PortfolioItem::CATEGORY_OPTIONS),
                SelectFilter::make('project_type')->options(PortfolioItem::PROJECT_TYPE_OPTIONS),
                SelectFilter::make('evidence_status')->options(PortfolioItem::EVIDENCE_STATUS_OPTIONS),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortfolioItems::route('/'),
            'create' => Pages\CreatePortfolioItem::route('/create'),
            'edit' => Pages\EditPortfolioItem::route('/{record}/edit'),
        ];
    }
}
