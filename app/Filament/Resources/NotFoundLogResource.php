<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotFoundLogResource\Pages;
use App\Models\NotFoundLog;
use App\Models\Redirect;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotFoundLogResource extends Resource
{
    protected static ?string $model = NotFoundLog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string | \UnitEnum | null $navigationGroup = 'SEO';

    protected static ?string $navigationLabel = '404 Monitor';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('path')->searchable(),
                TextColumn::make('referrer')->limit(50)->placeholder('—'),
                TextColumn::make('hits')->sortable(),
                TextColumn::make('last_hit_at')->dateTime()->sortable(),
            ])
            ->defaultSort('last_hit_at', 'desc')
            ->recordActions([
                Action::make('createRedirect')
                    ->label('Create Redirect')
                    ->icon('heroicon-o-arrow-turn-right-down')
                    ->form([
                        TextInput::make('from_path')->required()->default(fn (NotFoundLog $record) => $record->path)->disabled(),
                        TextInput::make('to_path')->required()->label('Redirect to'),
                        Select::make('status_code')->options([
                            301 => '301 — Permanent',
                            302 => '302 — Temporary',
                        ])->default(301)->required(),
                    ])
                    ->action(function (NotFoundLog $record, array $data): void {
                        Redirect::create([
                            'from_path' => $record->path,
                            'to_path' => $data['to_path'],
                            'status_code' => $data['status_code'],
                            'is_active' => true,
                        ]);
                        $record->delete();

                        Notification::make()->title('Redirect created')->success()->send();
                    }),
                DeleteAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotFoundLogs::route('/'),
        ];
    }
}
