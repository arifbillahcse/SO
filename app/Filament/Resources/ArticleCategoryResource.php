<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleCategoryResource\Pages;
use App\Models\ArticleCategory;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArticleCategoryResource extends Resource
{
    protected static ?string $model = ArticleCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Article Categories';

    /** @return array<Component> */
    public static function categoryFields(): array
    {
        return [
            TextInput::make('name')->required()->maxLength(255)->unique(ignoreRecord: true)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
            TextInput::make('slug')->required()->maxLength(255)->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                ->unique(ignoreRecord: true)->helperText('Used by the public article filter. Use lowercase words separated by hyphens.'),
            Textarea::make('description')->rows(3)->maxLength(1000)->columnSpanFull(),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(static::categoryFields());
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('slug')->searchable(),
            TextColumn::make('articles_count')->counts('articles')->label('Articles'),
            TextColumn::make('description')->limit(70)->toggleable(),
        ])->defaultSort('name')->recordActions([EditAction::make()]);
    }

    public static function canDelete(Model $record): bool
    {
        return parent::canDelete($record) && ! $record->articles()->exists();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticleCategories::route('/'),
            'create' => Pages\CreateArticleCategory::route('/create'),
            'edit' => Pages\EditArticleCategory::route('/{record}/edit'),
        ];
    }
}
