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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('role')->label('Role / company')->required()->maxLength(255),
            TextInput::make('avatar_initial')->maxLength(2)->regex('/^[\p{L}]{1,2}$/u')
                ->helperText('Optional: 1-2 letters. Leave blank to generate initials from the name.'),
            Select::make('stars')->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5'])
                ->default(5)->required()->rules(['integer', 'between:1,5']),
            Textarea::make('text')->label('Customer quote')->required()->rows(5)->columnSpanFull(),
            Toggle::make('featured')->label('Published on homepage')->default(false)
                ->helperText('Enable when this testimonial is ready to appear. Disable to keep it as a draft.'),
            TextInput::make('sort_order')->label('Display order')->integer()->minValue(0)->maxValue(65535)->default(0),
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
                IconColumn::make('featured')->label('Published')->boolean(),
                TextColumn::make('text')->limit(50),
            ])
            ->defaultSort('sort_order')->reorderable('sort_order')
            ->filters([TernaryFilter::make('featured')->label('Published')])
            ->emptyStateHeading('No testimonials yet')
            ->emptyStateDescription('Add customer feedback, then enable Published on homepage when it is ready.');
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
