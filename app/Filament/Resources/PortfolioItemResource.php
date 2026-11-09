<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasSeoCompletionColumn;
use App\Filament\Resources\PortfolioItemResource\Pages;
use App\Models\PortfolioItem;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PortfolioItemResource extends Resource
{
    use HasSeoCompletionColumn;

    protected static ?string $model = PortfolioItem::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Portfolio';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255)->live(onBlur: true)
                ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
            TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
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
            Textarea::make('description')->rows(3)->columnSpanFull()
                ->helperText('Short summary shown on the project detail page.'),
            RichEditor::make('body')->columnSpanFull()
                ->helperText('Full project write-up. Leave blank to show only the description.'),
            TextInput::make('sort_order')->numeric()->default(0),

            TextInput::make('meta_title')->label('SEO Title')->maxLength(255)
                ->helperText('Overrides the default page title. Leave blank to use the title above.'),
            Textarea::make('meta_description')->rows(3)->maxLength(320)->columnSpanFull()
                ->helperText('Shown under your title in Google search results.'),
            TextInput::make('focus_keywords')
                ->label('Focus Keywords')
                ->helperText('Comma-separated, up to 5, e.g. "wordpress migration, enterprise hosting"')
                ->afterStateHydrated(fn ($component, $state) => $component->state(is_array($state) ? implode(', ', $state) : $state))
                ->dehydrateStateUsing(fn ($state) => array_values(array_filter(array_map('trim', explode(',', $state ?? ''))))),
            Toggle::make('robots_index')->label('Allow search engines to index this page')->default(true),
            Toggle::make('robots_follow')->label('Allow search engines to follow links on this page')->default(true),
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
                static::seoCompletionColumn(),
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
