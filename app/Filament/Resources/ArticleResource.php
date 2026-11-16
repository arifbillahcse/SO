<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Seo\SeoFormSchema;
use App\Models\Article;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Articles';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255)->live(onBlur: true)
                ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
            TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            Select::make('article_category_id')->label('Category')
                ->relationship('articleCategory', 'name')->searchable()->preload()->required()
                ->createOptionForm(ArticleCategoryResource::categoryFields()),
            Textarea::make('excerpt')->required()->rows(3)->columnSpanFull(),
            Section::make('Featured image')
                ->description('Shown on the article page and article cards. Optional for existing articles.')
                ->schema([
                    FileUpload::make('featured_image_path')->label('Featured image')
                        ->image()->imageEditor()->disk('public')->directory('articles/featured')->visibility('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])->maxSize(5120)
                        ->helperText('JPG, PNG, WebP or GIF, up to 5 MB. A landscape image around 1200 x 675 px works well.')
                        ->columnSpanFull(),
                    TextInput::make('featured_image_alt')->label('Image alt text')->maxLength(255)
                        ->helperText('Describe the image for accessibility. Leave blank to use the article title.'),
                    Textarea::make('featured_image_caption')->label('Image caption')->rows(2)->maxLength(1000),
                ])->columns(2)->columnSpanFull(),
            RichEditor::make('body')->label('Article body')
                ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('articles/body')->fileAttachmentsVisibility('public')
                ->fileAttachmentsAcceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                ->fileAttachmentsMaxSize(5120)
                ->toolbarButtons([
                    ['bold', 'italic', 'underline', 'strike', 'link'],
                    ['h2', 'h3'],
                    ['alignStart', 'alignCenter', 'alignEnd'],
                    ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                    ['table', 'attachFiles'],
                    ['undo', 'redo'],
                ])
                ->helperText('To insert an image, place the cursor in the text and click the image / attach-files toolbar button. Upload an image and add its alt text. JPG, PNG, WebP or GIF, up to 5 MB.')
                ->columnSpanFull(),
            TextInput::make('read_minutes')->integer()->minValue(1)->maxValue(65535)->default(5)->required(),
            Toggle::make('featured')->helperText('Only one article should be featured at a time.'),
            DateTimePicker::make('published_at'),

            Section::make('SEO')
                ->description('How this page appears in Google and when shared. Saved values go live immediately.')
                ->relationship('seo')
                ->schema([SeoFormSchema::tabs('article')])
                ->collapsible()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image_path')->label('Image')->disk('public')->height(44)->width(72),
                TextColumn::make('title')->searchable(),
                TextColumn::make('articleCategory.name')->label('Category')->badge()->searchable(),
                TextColumn::make('read_minutes')->label('Min Read'),
                IconColumn::make('featured')->boolean(),
                TextColumn::make('published_at')->dateTime()->sortable(),
                SeoFormSchema::scoreColumn('seo.seo_score', sortable: false)->label('SEO'),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([SelectFilter::make('article_category_id')->label('Category')->relationship('articleCategory', 'name')]);
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
