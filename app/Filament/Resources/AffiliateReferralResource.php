<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AffiliateReferralResource\Pages;
use App\Models\AffiliateReferral;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AffiliateReferralResource extends Resource
{
    protected static ?string $model = AffiliateReferral::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-plus';

    protected static string | \UnitEnum | null $navigationGroup = 'Leads';

    protected static ?string $navigationLabel = 'Affiliate Referrals';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('referrer_name')->required()->maxLength(255)->disabled(),
            TextInput::make('referrer_email')->email()->required()->maxLength(255)->disabled(),
            TextInput::make('referrer_phone')->tel()->maxLength(255)->disabled(),
            Textarea::make('message')->rows(5)->columnSpanFull()->disabled(),
            Select::make('status')->options([
                'new' => 'New',
                'contacted' => 'Contacted',
                'converted' => 'Converted',
                'rejected' => 'Rejected',
            ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable()->label('Received'),
                TextColumn::make('referrer_name')->searchable(),
                TextColumn::make('referrer_email')->searchable(),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'new' => 'warning',
                    'contacted' => 'info',
                    'converted' => 'success',
                    'rejected' => 'danger',
                    default => 'gray',
                }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'new' => 'New',
                    'contacted' => 'Contacted',
                    'converted' => 'Converted',
                    'rejected' => 'Rejected',
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
            'index' => Pages\ListAffiliateReferrals::route('/'),
            'edit' => Pages\EditAffiliateReferral::route('/{record}/edit'),
        ];
    }
}
