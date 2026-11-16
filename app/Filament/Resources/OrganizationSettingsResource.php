<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationSettingsResource\Pages;
use App\Models\OrganizationSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrganizationSettingsResource extends Resource
{
    protected static ?string $model = OrganizationSettings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'SEO';

    protected static ?string $navigationLabel = 'Organization';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            FileUpload::make('logo')->image()->directory('seo')->visibility('public'),
            Textarea::make('description')->rows(3)->columnSpanFull(),
            TextInput::make('telephone')->tel()->maxLength(255),
            TextInput::make('email')->email()->maxLength(255),
            TextInput::make('facebook_url')->url()->label('Facebook URL')->maxLength(255),
            TextInput::make('twitter_url')->url()->label('Twitter / X URL')->maxLength(255),
            TextInput::make('linkedin_url')->url()->label('LinkedIn URL')->maxLength(255),
            TextInput::make('youtube_url')->url()->label('YouTube URL')->maxLength(255),
            Section::make('Analytics and Google verification')
                ->description('Installed on public website pages. Admin pages are excluded.')
                ->schema([
                    Select::make('tracking_mode')->label('Analytics installation')
                        ->options(['none' => 'Disabled', 'ga4' => 'Google Analytics directly (GA4)', 'gtm' => 'Google Tag Manager'])
                        ->default('ga4')->required()->rules(['in:none,ga4,gtm'])
                        ->helperText('Choose one installation. If Analytics is configured in Tag Manager, select Google Tag Manager here.'),
                    TextInput::make('google_analytics_id')->label('Google Analytics Measurement ID')->maxLength(100)
                        ->regex('/\\AG-[A-Z0-9]+\\z/')->placeholder('G-XXXXXXXXXX')
                        ->helperText('Paste the ID only. Used when Analytics installation is GA4; leave blank to disable.'),
                    TextInput::make('google_tag_manager_id')->label('Google Tag Manager Container ID')->maxLength(100)
                        ->regex('/\\AGTM-[A-Z0-9]+\\z/')->placeholder('GTM-XXXXXXX')
                        ->helperText('Paste the container ID only. Adds the head script and body-start noscript snippet when Tag Manager is selected.'),
                    TextInput::make('google_site_verification')->label('Google Search Console verification token')->maxLength(255)
                        ->regex('/\\A[A-Za-z0-9_-]+\\z/')->placeholder('Your verification token')
                        ->helperText('Paste only the content value from the google-site-verification meta tag. The meta tag is added to the head automatically. This is for HTML-tag verification.'),
                ])->columns(2)->columnSpanFull(),
            Section::make('Custom head and body code')
                ->description('For additional verification tags, analytics or other integrations.')
                ->schema([
                    Toggle::make('custom_code_enabled')->label('Enable custom code')->default(false)
                        ->helperText('Disable to stop all custom snippets while keeping their saved code. The Google fields above are controlled separately.'),
                    Textarea::make('custom_head_code')->label('Head code')->rows(8)->maxLength(50000)
                        ->extraInputAttributes(['class' => 'font-mono', 'spellcheck' => 'false'])
                        ->helperText('HTML or JavaScript inside <head>. Paste trusted integration code, including any required <script> or <meta> tags.'),
                    Textarea::make('custom_body_start_code')->label('Body start code')->rows(8)->maxLength(50000)
                        ->extraInputAttributes(['class' => 'font-mono', 'spellcheck' => 'false'])
                        ->helperText('Inserted immediately after the opening <body> tag, after any built-in Tag Manager noscript snippet.'),
                    Textarea::make('custom_body_end_code')->label('Body end code')->rows(8)->maxLength(50000)
                        ->extraInputAttributes(['class' => 'font-mono', 'spellcheck' => 'false'])
                        ->helperText('Inserted just before </body>. Avoid installing a tag here that is already enabled in the Google fields above.'),
                ])->columnSpanFull(),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name'),
            TextColumn::make('email'),
            TextColumn::make('telephone'),
        ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizationSettings::route('/'),
            'edit' => Pages\EditOrganizationSettings::route('/{record}/edit'),
        ];
    }
}
