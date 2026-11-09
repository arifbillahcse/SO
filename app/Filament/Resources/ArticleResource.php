<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Articles';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255)->live(onBlur: true)
                ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
            TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            Select::make('category')->options([
                'WordPress' => 'WordPress',
                'SEO' => 'SEO',
                'Cloud' => 'Cloud',
                'Automation' => 'Automation',
                'eCommerce' => 'eCommerce',
            ])->required(),
            Textarea::make('excerpt')->required()->rows(3)->columnSpanFull(),
            RichEditor::make('body')->columnSpanFull(),
            TextInput::make('read_minutes')->numeric()->default(5)->required(),
            Toggle::make('featured')->helperText('Only one article should be featured at a time.'),
            DateTimePicker::make('published_at'),

            TextInput::make('meta_title')->label('SEO Title')->maxLength(255)
                ->helperText('Overrides the default page title. Leave blank to use the title above.'),
            Textarea::make('meta_description')->rows(3)->maxLength(320)->columnSpanFull()
                ->helperText('Shown under your title in Google search results. Leave blank to use the excerpt.'),
            TextInput::make('focus_keywords')
                ->label('Focus Keywords')
                ->helperText('Comma-separated, up to 5, e.g. "wordpress speed optimization"')
                ->afterStateHydrated(fn ($component, $state) => $component->state(is_array($state) ? implode(', ', $state) : $state))
                ->dehydrateStateUsing(fn ($state) => array_values(array_filter(array_map('trim', explode(',', $state ?? ''))))),
            FileUpload::make('og_image')->label('Open Graph Image')->image()->directory('seo')->visibility('public')
                ->helperText('Shown when this article is shared on social media.'),
            Toggle::make('robots_index')->label('Allow search engines to index this page')->default(true),
            Toggle::make('robots_follow')->label('Allow search engines to follow links on this page')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('category')->badge(),
                TextColumn::make('read_minutes')->label('Min Read'),
                IconColumn::make('featured')->boolean(),
                TextColumn::make('published_at')->dateTime()->sortable(),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
