<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers\OfferingsRelationManager;
use App\Filament\Seo\SeoFormSchema;
use App\Models\Service;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Services';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255)->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique(ignoreRecord: true),
            Select::make('route_name')->label('Destination page')->options(static::pageOptions())->required()
                ->rules([Rule::in(array_keys(static::pageOptions()))])->unique(ignoreRecord: true),
            Select::make('icon')->options(Service::ICON_OPTIONS)->required(),
            TextInput::make('short_description')->required()->maxLength(255)->columnSpanFull(),
            Repeater::make('features')
                ->simple(TextInput::make('feature')->required())
                ->columnSpanFull(),
            Toggle::make('featured')->helperText('Shown as the highlighted card on the homepage.'),
            Toggle::make('is_published')->label('Published')->default(true)
                ->helperText('Unpublishing hides this group and its offerings from the homepage and catalogue.'),
            TextInput::make('sort_order')->integer()->minValue(0)->maxValue(65535)->default(0),

            Section::make('SEO for this service\'s page')
                ->description('The service is presented on its own page (see Route name). These settings control how that page appears in Google.')
                ->relationship('pageSeo')
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
                TextColumn::make('route_name')->badge(),
                IconColumn::make('featured')->boolean(),
                IconColumn::make('is_published')->label('Published')->boolean(),
                TextColumn::make('offerings_count')->counts('offerings')->label('Offerings'),
                SeoFormSchema::scoreColumn('pageSeo.seo_score', sortable: false)->label('Page SEO'),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')->reorderable('sort_order');
    }

    public static function pageOptions(): array
    {
        return collect(config('seo.pages'))->mapWithKeys(fn (array $page, string $name): array => [$name => $page['label']])->all();
    }

    public static function getRelations(): array
    {
        return [OfferingsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
