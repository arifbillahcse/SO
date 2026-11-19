<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiagnosticPageSettingsResource\Pages;
use App\Models\DiagnosticPageSettings;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DiagnosticPageSettingsResource extends Resource
{
    protected static ?string $model = DiagnosticPageSettings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'DiagnoCare Plans';

    protected static ?string $modelLabel = 'DiagnoCare plans';

    protected static ?string $pluralModelLabel = 'DiagnoCare Plans';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Plans section')->schema([
                Toggle::make('plans_enabled')->label('Show plans section')->default(true)
                    ->helperText('Turning this off also removes named plans from the demo form. Saved plans are kept.'),
                TextInput::make('plans_title')->label('Section heading')->required()->maxLength(255),
                Textarea::make('plans_introduction')->label('Introduction / payment information')->rows(3)->maxLength(2000),
            ])->columnSpanFull(),
            Section::make('Plan cards')->description('Add, remove or drag plans into order. Changes also update the demo dropdown and plan validation.')->schema([
                Repeater::make('plans')->label('Plans')->schema([
                    TextInput::make('name')->label('Plan name')->required()->maxLength(100)->distinct()->rules(['not_in:Not sure yet'])
                        ->helperText('Use a unique name. Review feature references to other plans after renaming.'),
                    TextInput::make('price')->label('Price / billing text')->required()->maxLength(255)
                        ->helperText('For example: Ask for price, Free, or BDT 1,500 / month.'),
                    TextInput::make('audience')->label('Who this plan is for')->maxLength(255),
                    TextInput::make('badge')->label('Badge text')->maxLength(100),
                    Toggle::make('is_highlighted')->label('Highlight this plan')->default(false),
                    Toggle::make('is_enabled')->label('Publish this plan')->default(true),
                    Repeater::make('features')->label('Included features')
                        ->simple(TextInput::make('feature')->required()->maxLength(255))
                        ->maxItems(30)->defaultItems(0)->addActionLabel('Add feature')->columnSpanFull(),
                    TextInput::make('button_label')->label('Button text')->required()->maxLength(100)
                        ->helperText('Buttons select this plan in the demo request form.'),
                ])->columns(2)->maxItems(12)->defaultItems(0)->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                    ->addActionLabel('Add plan')->columnSpanFull(),
            ])->columnSpanFull(),
            Section::make('Related FAQ and demo form')->schema([
                TextInput::make('pricing_faq_question')->label('Pricing FAQ question')->required()->maxLength(255),
                Textarea::make('pricing_faq_answer')->label('Pricing FAQ answer')->required()->maxLength(3000)
                    ->helperText('Used in the visible FAQ and search structured data. Review this answer when changing prices or billing terms.'),
                TextInput::make('demo_plan_label')->label('Demo form plan label')->required()->maxLength(100),
                TextInput::make('undecided_plan_label')->label('No plan selected label')->required()->maxLength(100),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('plans_title')->label('Plans heading'),
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
            'index' => Pages\ListDiagnosticPageSettings::route('/'),
            'edit' => Pages\EditDiagnosticPageSettings::route('/{record}/edit'),
        ];
    }
}
