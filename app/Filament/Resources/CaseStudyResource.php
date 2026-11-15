<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CaseStudyResource\Pages;
use App\Filament\Seo\SeoFormSchema;
use App\Models\CaseStudy;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CaseStudyResource extends Resource
{
    protected static ?string $model = CaseStudy::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            TextInput::make('client')->maxLength(255),
            TextInput::make('tags')->required()->helperText('Comma-separated, e.g. "eCommerce, WooCommerce"')
                ->afterStateHydrated(fn ($component, $state) => $component->state(is_array($state) ? implode(', ', $state) : $state))
                ->dehydrateStateUsing(fn ($state) => array_values(array_filter(array_map('trim', explode(',', $state))))),
            Textarea::make('description')->required()->rows(4)->columnSpanFull(),
            Repeater::make('metrics')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('value')->required(),
                        TextInput::make('label')->required(),
                    ]),
                ])
                ->columnSpanFull()
                ->defaultItems(3),
            RichEditor::make('body')->columnSpanFull()
                ->helperText('Full case study write-up. Leave blank to show only the description above.'),
            Toggle::make('featured')->helperText('Only one case study should be featured at a time.'),
            TextInput::make('sort_order')->numeric()->default(0),

            Section::make('SEO')
                ->description('How this page appears in Google and when shared. Saved values go live immediately.')
                ->relationship('seo')
                ->schema([SeoFormSchema::tabs()])
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
                IconColumn::make('featured')->boolean(),
                SeoFormSchema::scoreColumn('seo.seo_score', sortable: false)->label('SEO'),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCaseStudies::route('/'),
            'create' => Pages\CreateCaseStudy::route('/create'),
            'edit' => Pages\EditCaseStudy::route('/{record}/edit'),
        ];
    }
}
