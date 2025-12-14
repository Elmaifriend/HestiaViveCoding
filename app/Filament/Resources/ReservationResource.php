<?php

namespace App\Filament\Resources;

use App\Enums\ReservationStatus;
use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Instalaciones y Reservas';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $modelLabel = 'Reserva';

    protected static ?string $pluralModelLabel = 'Reservas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Reservation Details')
                    ->schema([
                        Select::make('resident_id')
                            ->relationship('resident', 'name')
                            ->label('Resident')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('amenity_id')
                            ->relationship('amenity', 'name')
                            ->label('Amenity')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DateTimePicker::make('date')
                            ->label('Start Time')
                            ->required(),
                        DateTimePicker::make('end_time')
                            ->label('End Time')
                            ->required()
                            ->after('date'),
                        Select::make('status')
                            ->options(ReservationStatus::class)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('resident.name')
                    ->label('Resident')
                    ->searchable(),
                TextColumn::make('amenity.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ReservationStatus::class),
                SelectFilter::make('amenity')
                    ->relationship('amenity', 'name'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
