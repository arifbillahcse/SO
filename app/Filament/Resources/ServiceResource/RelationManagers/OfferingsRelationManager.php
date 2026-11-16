<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use App\Filament\Resources\ServiceResource;
use App\Models\Service;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OfferingsRelationManager extends RelationManager
{
    protected static string $relationship = 'offerings';

    protected static ?string $title = 'Detailed services';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('service_id')->label('Core service group')
                ->options(fn (): array => Service::orderBy('sort_order')->pluck('title', 'id')->all())
                ->default(fn (): int => $this->getOwnerRecord()->getKey())->required()->exists('services', 'id')
                ->disabled(fn (string $operation): bool => $operation === 'create')
                ->helperText('New offerings belong to this group. Edit an offering to move it to another group.'),
            TextInput::make('title')->required()->maxLength(255)->live(onBlur: true)
                ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
            TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true)
                ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                ->helperText('Used for the card link on the services page.'),
            Select::make('icon')->options(Service::ICON_OPTIONS)
                ->helperText('Leave blank to use the core service icon.'),
            Textarea::make('description')->required()->rows(3)->columnSpanFull(),
            Repeater::make('scope')->label('Scope items')
                ->simple(TextInput::make('item')->required()->maxLength(255))->columnSpanFull(),
            TextInput::make('availability')->maxLength(255)
                ->helperText('Optional visible note, such as Under development.'),
            Toggle::make('is_published')->label('Published')->default(false)
                ->helperText('Drafts are excluded from the public catalogue and structured data.'),
            Toggle::make('featured')->label('Featured')->default(false),
            TextInput::make('sort_order')->integer()->minValue(0)->maxValue(65535)->default(0),
            Section::make('Button')->description('Choose an existing page or enter an external HTTP/HTTPS URL.')->schema([
                TextInput::make('action_label')->label('Button text')->required()->maxLength(255),
                Select::make('action_route')->label('Destination page')->options(ServiceResource::pageOptions())->live()
                    ->required(fn (Get $get): bool => blank($get('action_url')))
                    ->rules(fn (Get $get): array => filled($get('action_url')) ? ['prohibited'] : [Rule::in(array_keys(ServiceResource::pageOptions()))]),
                TextInput::make('action_fragment')->label('Section anchor')->maxLength(255)
                    ->regex('/^[a-zA-Z][a-zA-Z0-9_-]*$/')
                    ->helperText('For example: contact. Enter without #. Applies to destination pages.'),
                TextInput::make('action_url')->label('External URL')->url()->rules(['url:http,https'])->maxLength(2048)->live(onBlur: true)
                    ->required(fn (Get $get): bool => blank($get('action_route'))),
            ])->columns(2)->columnSpanFull(),
            Select::make('portfolioItems')->label('Related portfolio projects')->multiple()
                ->relationship('portfolioItems', 'title')->searchable()->preload()->columnSpanFull(),
            TextInput::make('catalogue_key')->label('Import reference')->disabled()->dehydrated(false)
                ->helperText('Kept stable so future imports preserve your edits.'),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('sort_order')->label('#')->sortable(),
                TextColumn::make('title')->searchable(),
                IconColumn::make('is_published')->label('Published')->boolean(),
                IconColumn::make('featured')->boolean(),
                TextColumn::make('availability')->toggleable(),
            ])
            ->filters([TernaryFilter::make('is_published')->label('Published')])
            ->defaultSort('sort_order')->reorderable('sort_order')
            ->headerActions([CreateAction::make()->label('New detailed service')])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
