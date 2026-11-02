<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RedirectResource\Pages;
use App\Models\Redirect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-turn-right-down';

    protected static string | \UnitEnum | null $navigationGroup = 'SEO';

    protected static ?string $navigationLabel = 'Redirects';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('from_path')->required()->maxLength(255)->unique(ignoreRecord: true)
                ->helperText('The old path, e.g. "/old-page" (must start with /).')
                ->rule('regex:/^\//'),
            TextInput::make('to_path')->required()->maxLength(255)
                ->helperText('Where visitors should land instead, e.g. "/our-works" or a full URL.'),
            Select::make('status_code')->options([
                301 => '301 — Permanent',
                302 => '302 — Temporary',
            ])->default(301)->required(),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_path')->searchable(),
                TextColumn::make('to_path')->searchable(),
                TextColumn::make('status_code')->label('Type')->badge(),
                TextColumn::make('hits')->sortable(),
                TextColumn::make('last_hit_at')->dateTime()->sortable()->placeholder('Never'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRedirects::route('/'),
            'create' => Pages\CreateRedirect::route('/create'),
            'edit' => Pages\EditRedirect::route('/{record}/edit'),
        ];
    }
}
