<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('role')->required()->maxLength(255),
            TextInput::make('avatar_initial')->required()->maxLength(2)
                ->helperText('1-2 letters shown in the avatar circle.'),
            Select::make('stars')->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5'])
                ->default(5)->required(),
            Textarea::make('text')->required()->rows(4)->columnSpanFull(),
            Toggle::make('featured')->default(true)->helperText('Only featured testimonials show on the homepage carousel.'),
            TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->sortable()->label('#'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('role'),
                TextColumn::make('stars'),
                IconColumn::make('featured')->boolean(),
                TextColumn::make('text')->limit(50),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
