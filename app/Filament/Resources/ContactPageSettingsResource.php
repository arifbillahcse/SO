<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactPageSettingsResource\Pages;
use App\Models\ContactPageSettings;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContactPageSettingsResource extends Resource
{
    protected static ?string $model = ContactPageSettings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Contact Page';

    protected static ?string $modelLabel = 'contact page';

    protected static ?string $pluralModelLabel = 'Contact Page';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Page introduction')->schema([
                TextInput::make('hero_tag')->label('Small heading')->required()->maxLength(255),
                TextInput::make('hero_title')->label('Main heading')->required()->maxLength(255),
                Textarea::make('hero_description')->label('Introduction')->required()->rows(4)->maxLength(2000)->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
            Section::make('Contact details')->description('Blank phone and email fields use the values in SEO > Organization.')->schema([
                TextInput::make('contact_email')->label('Public email')->email()->maxLength(255),
                TextInput::make('contact_phone')->label('Public phone')->tel()->maxLength(50),
                TextInput::make('map_url')->label('Map link')->url()->maxLength(2048)->rules(['starts_with:https://'])
                    ->helperText('Optional HTTPS link to your location.'),
                Textarea::make('address')->rows(3)->maxLength(2000),
                Textarea::make('business_hours')->rows(3)->maxLength(2000),
            ])->columns(2)->columnSpanFull(),
            Section::make('WhatsApp chat')->description('A floating chat button at the bottom-right of public website pages.')->schema([
                Toggle::make('whatsapp_chat_enabled')->label('Show WhatsApp chat button')->default(true)->live()
                    ->helperText('Turn off to hide the floating button. Your saved number is kept.'),
                TextInput::make('whatsapp_number')->label('WhatsApp number')->tel()->maxLength(20)
                    ->required(fn (Get $get): bool => (bool) $get('whatsapp_chat_enabled'))
                    ->regex('/\A\+?[1-9][0-9]{6,14}\z/')
                    ->helperText('Include country code. Digits only, with an optional leading +, for example +8801779440297. This also updates the WhatsApp link on the contact page.'),
            ])->columns(2)->columnSpanFull(),
            Section::make('Message form')->schema([
                TextInput::make('form_title')->label('Form heading')->required()->maxLength(255),
                TextInput::make('submit_label')->label('Send button text')->required()->maxLength(100),
                Textarea::make('form_description')->label('Form introduction')->required()->maxLength(2000)->columnSpanFull(),
                Textarea::make('success_message')->label('Confirmation message')->required()->maxLength(1000)->columnSpanFull(),
                TextInput::make('notification_email')->label('Primary notification email')->email()->maxLength(255)
                    ->helperText('Private inbox for new enquiries and affiliate referrals. This does not change the public contact email.'),
                Repeater::make('additional_notification_emails')->label('Additional notification emails')
                    ->simple(TextInput::make('email')->email()->required()->maxLength(255))
                    ->maxItems(20)->defaultItems(0)->addActionLabel('Add recipient')
                    ->helperText('Each address receives the full message. Duplicates are removed. If all recipient fields are empty, the configured default inbox is used.'),
            ])->columns(2)->columnSpanFull(),
            Section::make('Next steps and questions')->schema([
                Repeater::make('next_steps')->label('What happens next')->schema([
                    TextInput::make('title')->required()->maxLength(255),
                    Textarea::make('description')->required()->maxLength(1500),
                ])->maxItems(6)->defaultItems(0)->collapsible()->columnSpanFull(),
                Repeater::make('faqs')->label('Frequently asked questions')->schema([
                    TextInput::make('question')->required()->maxLength(255),
                    Textarea::make('answer')->required()->maxLength(2000),
                ])->maxItems(12)->defaultItems(0)->collapsible()->columnSpanFull(),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('hero_title')->label('Contact page heading'),
            TextColumn::make('updated_at')->label('Last updated')->dateTime(),
        ])->recordActions([EditAction::make()]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactPageSettings::route('/'),
            'edit' => Pages\EditContactPageSettings::route('/{record}/edit'),
        ];
    }
}
