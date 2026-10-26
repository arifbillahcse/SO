<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PortfolioItemResource\Pages;
use App\Models\PortfolioItem;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PortfolioItemResource extends Resource
{
    protected static ?string $model = PortfolioItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Portfolio';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('client')->required()->maxLength(255),
            Select::make('category')->options([
                'wordpress' => 'WordPress',
                'ecommerce' => 'eCommerce',
                'cloud' => 'Cloud',
                'automation' => 'Automation',
                'seo' => 'SEO',
            ])->required(),
            TextInput::make('tags')->required()->helperText('Comma-separated, e.g. "WordPress, Enterprise"')
                ->afterStateHydrated(fn ($component, $state) => $component->state(is_array($state) ? implode(', ', $state) : $state))
                ->dehydrateStateUsing(fn ($state) => array_values(array_filter(array_map('trim', explode(',', $state))))),
            FileUpload::make('thumbnail_path')->image()->directory('portfolio')->visibility('public'),
            TextInput::make('sort_order')->numeric()->default(0),
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
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('category')->options([
                    'wordpress' => 'WordPress',
                    'ecommerce' => 'eCommerce',
                    'cloud' => 'Cloud',
                    'automation' => 'Automation',
                    'seo' => 'SEO',
                ]),
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
