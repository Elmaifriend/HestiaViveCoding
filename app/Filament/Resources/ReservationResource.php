<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Reservation;
use Filament\Schemas\Schema;
use App\Enums\ReservationStatus;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\ReservationResource\Pages;
use Filament\Forms\Components\Textarea; // Agregado por si quieres notas

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Instalaciones y Reservas';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days'; // Icono más moderno

    protected static ?string $modelLabel = 'Reserva';

    protected static ?string $pluralModelLabel = 'Reservas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos de la Solicitud')
                    ->columns(2)
                    ->columnSpanFull()
                    ->description('Gestione quién reserva y en qué horario.')
                    ->icon('heroicon-o-ticket')
                    ->schema([
                        Select::make('resident_id')
                            ->relationship('resident', 'name')
                            ->label('Residente')
                            ->placeholder('Buscar residente...')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->prefixIcon('heroicon-m-user'),

                        Select::make('amenity_id')
                            ->relationship('amenity', 'name')
                            ->label('Amenidad / Área')
                            ->placeholder('Seleccionar área...')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->prefixIcon('heroicon-m-building-office-2'),

                        DateTimePicker::make('date')
                            ->label('Inicio de Reserva')
                            ->required()
                            ->seconds(false)
                            ->native(false)
                            ->prefixIcon('heroicon-m-clock'),

                        DateTimePicker::make('end_time')
                            ->label('Fin de Reserva')
                            ->required()
                            ->seconds(false)
                            ->native(false)
                            ->after('date')
                            ->prefixIcon('heroicon-m-clock'),

                        Select::make('status')
                            ->label('Estado Actual')
                            ->options([
                                'pending' => 'Pendiente',
                                'approved' => 'Confirmada',
                                'rejected' => 'Rechazada',
                                'cancelled' => 'Cancelada',
                            ])
                            ->required()
                            ->native(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('resident.name')
                    ->label('Solicitante')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->description(fn(Reservation $record) => $record->resident->email ?? ''),

                TextColumn::make('amenity.name')
                    ->label('Área Reservada')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('date')
                    ->label('Horario')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->description(fn(Reservation $record) => 'Hasta: ' . ($record->end_time?->format('d M Y, h:i A') ?? 'Indefinida')),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state->value ?? $state) {
                        'pending' => 'Pendiente',
                        'approved' => 'Confirmada',
                        'rejected' => 'Rechazada',
                        'cancelled' => 'Cancelada',
                        default => $state,
                    })
                    ->color(fn($state) => match ($state->value ?? $state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn($state) => match ($state->value ?? $state) {
                        'pending' => 'heroicon-m-clock',
                        'approved' => 'heroicon-m-check-circle',
                        'rejected' => 'heroicon-m-x-circle',
                        'cancelled' => 'heroicon-m-no-symbol',
                        default => null,
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Solicitado el')
                    ->dateTime('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Filtrar por Estado')
                    ->options(ReservationStatus::class),

                SelectFilter::make('amenity')
                    ->label('Filtrar por Área')
                    ->relationship('amenity', 'name'),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Modificar Reserva'),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Cancelar/Eliminar'),
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
