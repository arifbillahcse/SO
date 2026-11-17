<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientBrandResource\Pages;
use App\Models\ClientBrand;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientBrandResource extends Resource
{
    protected static ?string $model = ClientBrand::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Client Brands';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Brand name')->required()->maxLength(255)->unique(ignoreRecord: true),
            FileUpload::make('logo_path')->label('Brand logo')->image()
                ->disk('public')->directory('brands')->visibility('public')
                ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                ->rules(['extensions:png,jpg,jpeg,webp'])->maxSize(2048)
                ->helperText('PNG, JPG or WebP, up to 2 MB. A transparent PNG works well. Leave blank to display the brand name.'),
            Toggle::make('is_published')->label('Show on homepage')->default(true)
                ->helperText('Turn off to hide this brand while keeping its logo and settings.'),
            TextInput::make('sort_order')->label('Sort order')->integer()->minValue(0)->maxValue(65535)->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('sort_order')->label('#')->sortable(),
            ImageColumn::make('logo_path')->label('Logo')->disk('public')->visibility('public')->imageHeight(40),
            TextColumn::make('name')->searchable()->sortable(),
            IconColumn::make('is_published')->label('Shown')->boolean(),
        ])->defaultSort('sort_order')->reorderable('sort_order')->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientBrands::route('/'),
            'create' => Pages\CreateClientBrand::route('/create'),
            'edit' => Pages\EditClientBrand::route('/{record}/edit'),
        ];
    }
}
