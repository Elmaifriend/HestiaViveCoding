<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Amenity;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\AmenityResource\Pages;

class AmenityResource extends Resource
{
    protected static ?string $model = Amenity::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Instalaciones y Reservas';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $modelLabel = 'Amenidad';

    protected static ?string $pluralModelLabel = 'Amenidades';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Detalles básicos de la instalación o área común.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre de la Amenidad')
                            ->placeholder('Ej. Gimnasio, Salón de Usos Múltiples')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-tag')
                            ->columnSpan(2),

                        TextInput::make('location')
                            ->label('Ubicación Exacta')
                            ->placeholder('Ej. Torre A, Planta Baja')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-map-pin')
                            ->columnSpan(1),

                        TextInput::make('capacity')
                            ->label('Aforo Máximo')
                            ->numeric()
                            ->default(10)
                            ->required()
                            ->prefixIcon('heroicon-m-users')
                            ->suffix('personas')
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->label('Normas y Descripción')
                            ->placeholder('Indica las reglas de uso, horarios o equipamiento disponible...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Galería')
                    ->description('Imagen visible para los residentes en la app.')
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->schema([
                        FileUpload::make('photo_url')
                            ->label('Fotografía de Portada')
                            ->image()
                            ->imageEditor()
                            ->openable()
                            ->imagePreviewHeight('250')
                            ->panelAspectRatio('2:1')
                            ->panelLayout('integrated')
                            ->directory('amenities')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_url')
                    ->label('Vista')
                    ->disk('public'),

                TextColumn::make('name')
                    ->label('Amenidad')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Amenity $record) => $record->location)
                    ->wrap(),

                TextColumn::make('capacity')
                    ->label('Capacidad')
                    ->sortable()
                    ->badge()
                    ->numeric()
                    ->icon('heroicon-m-user-group')
                    ->color('primary')
                    ->formatStateUsing(fn(string $state): string => "{$state} Pers."),

                TextColumn::make('created_at')
                    ->label('Alta en Sistema')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-m-calendar-days')
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListAmenities::route('/'),
            'create' => Pages\CreateAmenity::route('/create'),
            'edit' => Pages\EditAmenity::route('/{record}/edit'),
        ];
    }
}
