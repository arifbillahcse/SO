<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamMemberResource\Pages;
use App\Models\TeamMember;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Team / Founders';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('role')->required()->maxLength(255)->helperText('Full title, e.g. "Founder & Chief Executive Officer"'),
            TextInput::make('short_role')->required()->maxLength(255)->helperText('Short badge label, e.g. "Founder & CEO"'),
            Textarea::make('bio')->required()->rows(6)->columnSpanFull()
                ->helperText('Separate paragraphs with a blank line.'),
            FileUpload::make('photo_path')->image()->directory('team')->visibility('public'),
            TextInput::make('email')->email()->maxLength(255),
            TextInput::make('website_url')->url()->maxLength(255),
            TextInput::make('credentials_heading')->required()->maxLength(255)->default('Recognition & Roles'),
            Repeater::make('credentials')
                ->simple(TextInput::make('credential')->required())
                ->columnSpanFull(),
            Repeater::make('skills')
                ->simple(TextInput::make('skill')->required())
                ->columnSpanFull(),
            Repeater::make('stats')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('value')->required(),
                        TextInput::make('label')->required(),
                    ]),
                ])
                ->columnSpanFull()
                ->defaultItems(4),
            TextInput::make('sort_order')->numeric()->default(0)
                ->helperText('Order 0/1 alternates the card layout left/right on the page.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->sortable()->label('#'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('short_role'),
                TextColumn::make('email'),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }
}
