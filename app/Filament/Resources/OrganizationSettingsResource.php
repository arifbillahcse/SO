<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationSettingsResource\Pages;
use App\Models\OrganizationSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrganizationSettingsResource extends Resource
{
    protected static ?string $model = OrganizationSettings::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office';

    protected static string | \UnitEnum | null $navigationGroup = 'SEO';

    protected static ?string $navigationLabel = 'Organization';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            FileUpload::make('logo')->image()->directory('seo')->visibility('public'),
            Textarea::make('description')->rows(3)->columnSpanFull(),
            TextInput::make('telephone')->tel()->maxLength(255),
            TextInput::make('email')->email()->maxLength(255),
            TextInput::make('facebook_url')->url()->label('Facebook URL')->maxLength(255),
            TextInput::make('twitter_url')->url()->label('Twitter / X URL')->maxLength(255),
            TextInput::make('linkedin_url')->url()->label('LinkedIn URL')->maxLength(255),
            TextInput::make('youtube_url')->url()->label('YouTube URL')->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name'),
            TextColumn::make('email'),
            TextColumn::make('telephone'),
        ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizationSettings::route('/'),
            'edit' => Pages\EditOrganizationSettings::route('/{record}/edit'),
        ];
    }
}
