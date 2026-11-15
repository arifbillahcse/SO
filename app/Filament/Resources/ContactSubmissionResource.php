<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactSubmissionResource\Pages;
use App\Models\ContactSubmission;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactSubmissionResource extends Resource
{
    protected static ?string $model = ContactSubmission::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-inbox';

    protected static string | \UnitEnum | null $navigationGroup = 'Leads';

    protected static ?string $navigationLabel = 'Contact Submissions';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255)->disabled(),
            TextInput::make('email')->email()->required()->maxLength(255)->disabled(),
            TextInput::make('phone')->tel()->maxLength(255)->disabled(),
            TextInput::make('service_interested')->maxLength(255)->disabled(),
            Textarea::make('message')->rows(5)->columnSpanFull()->disabled(),
            Select::make('status')->options([
                'new' => 'New',
                'contacted' => 'Contacted',
                'closed' => 'Closed',
            ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable()->label('Received'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('service_interested'),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'new' => 'warning',
                    'contacted' => 'info',
                    'closed' => 'success',
                    default => 'gray',
                }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'new' => 'New',
                    'contacted' => 'Contacted',
                    'closed' => 'Closed',
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactSubmissions::route('/'),
            'edit' => Pages\EditContactSubmission::route('/{record}/edit'),
        ];
    }
}
