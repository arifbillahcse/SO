<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LegalPagesResource\Pages;
use App\Models\OrganizationSettings;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LegalPagesResource extends Resource
{
    protected static ?string $model = OrganizationSettings::class;

    protected static ?string $slug = 'legal-pages';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Legal Pages';

    protected static ?string $modelLabel = 'legal pages';

    protected static ?string $pluralModelLabel = 'Legal Pages';

    public static function form(Schema $schema): Schema
    {
        $sections = [];

        foreach (['tos' => 'Terms & Conditions', 'privacy' => 'Privacy Policy'] as $page => $label) {
            $sections[] = Section::make($label)->description("Public URL: /{$page}. Review the starter wording against your actual business practices and applicable requirements before publishing it on your live website.")
                ->schema([
                    TextInput::make("{$page}_title")->label('Page heading')->required()->maxLength(255),
                    DatePicker::make("{$page}_effective_date")->label('Effective date')->required()->native(false),
                    Textarea::make("{$page}_intro")->label('Introduction')->required()->maxLength(1000)->rows(3)->columnSpanFull(),
                    RichEditor::make("{$page}_body")->label('Page content')->required()
                        ->toolbarButtons(['h2', 'h3', 'bold', 'italic', 'link', 'bulletList', 'orderedList', 'blockquote', 'undo', 'redo'])
                        ->columnSpanFull(),
                ])->columns(2)->columnSpanFull();
        }

        return $schema->components($sections);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Website'),
            TextColumn::make('updated_at')->label('Last saved')->dateTime(),
        ])->recordActions([EditAction::make()->label('Edit both pages')]);
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
            'index' => Pages\ListLegalPages::route('/'),
            'edit' => Pages\EditLegalPages::route('/{record}/edit'),
        ];
    }
}
