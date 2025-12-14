<?php

namespace App\Filament\Resources;

use App\Enums\IncidentStatus;
use App\Filament\Resources\IncidentResource\Pages;
use App\Models\Incident;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Comunidad';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $modelLabel = 'Incidente';

    protected static ?string $pluralModelLabel = 'Incidentes';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Incident Details')
                    ->schema([
                        Select::make('resident_id')
                            ->relationship('resident', 'name')
                            ->label('Resident')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->options(IncidentStatus::class)
                            ->required(),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('photo_url')
                            ->label('Photo')
                            ->image()
                            ->directory('incidents')
                            ->visibility('public')
                            ->openable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('resident.name')
                    ->label('Resident')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                ImageColumn::make('photo_url')
                    ->label('Photo')
                    ->disk('public'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(IncidentStatus::class),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncidents::route('/'),
            'create' => Pages\CreateIncident::route('/create'),
            'edit' => Pages\EditIncident::route('/{record}/edit'),
        ];
    }
}
