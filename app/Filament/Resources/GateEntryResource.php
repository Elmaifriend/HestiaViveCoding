<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\GateEntry;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Group;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\GateEntryResource\Pages;

class GateEntryResource extends Resource
{
    protected static ?string $model = GateEntry::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Accesos y Seguridad';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-key';

    protected static ?string $modelLabel = 'Acceso';

    protected static ?string $pluralModelLabel = 'Bitácora de Accesos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Información del Visitante')
                    ->columnSpan(7)
                    ->description('Detalles de la persona que solicita ingresar.')
                    ->icon('heroicon-m-identification')
                    ->schema([
                        Forms\Components\TextInput::make('guest_name')
                            ->label('Nombre del Visitante')
                            ->placeholder('Ej. Juan Pérez (Repartidor)')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-user')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('resident_id')
                            ->relationship('resident', 'name')
                            ->label('Autorizado por (Residente)')
                            ->placeholder('Buscar residente...')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->prefixIcon('heroicon-m-home-modern'),

                        Forms\Components\DateTimePicker::make('entry_date')
                            ->label('Fecha y Hora de Acceso')
                            ->required()
                            ->native(false)
                            ->seconds(false)
                            ->default(now())
                            ->prefixIcon('heroicon-m-clock'),
                    ])->columns(2),

                Section::make('Panel de Decisión')
                    ->description('Autorice o deniegue el ingreso con un solo clic.')
                    ->columnSpan(5)
                    ->schema([
                        ToggleButtons::make('status')
                            ->columnSpanFull()
                            ->hiddenLabel()
                            ->options([
                                'pending' => 'Pendiente',
                                'approved' => 'Aprobado',
                                'denied' => 'Denegado',
                            ])
                            ->required()
                            ->extraAttributes(['style' => 'margin: 40px auto'])
                            ->grouped()
                            ->default('pending')
                            ->icons([
                                'pending' => 'heroicon-m-clock',
                                'approved' => 'heroicon-m-check-circle',
                                'denied' => 'heroicon-m-no-symbol',
                            ])
                            ->colors([
                                'pending' => 'warning',
                                'approved' => 'success',
                                'denied' => 'danger',
                            ]),
                    ])
                    ->compact(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guest_name')
                    ->label('Visitante')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->description(fn(GateEntry $record) => 'Visita a: ' . ($record->resident->name ?? 'N/A')),

                TextColumn::make('entry_date')
                    ->label('Fecha de Ingreso')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->icon('heroicon-m-calendar-days')
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state->value ?? $state) {
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'denied' => 'Denegado',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state->value ?? $state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'denied' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn ($state) => match ($state->value ?? $state) {
                        'pending' => 'heroicon-m-clock',
                        'approved' => 'heroicon-m-check-circle',
                        'denied' => 'heroicon-m-no-symbol',
                        default => null,
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('entry_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Filtrar por Estado')
                    // Traducción Manual en el Filtro
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'denied' => 'Denegado',
                    ]),

                SelectFilter::make('resident')
                    ->label('Por Residente')
                    ->relationship('resident', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Editar Acceso'),
                DeleteAction::make()
                    ->iconButton(),
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
            'index' => Pages\ListGateEntries::route('/'),
            'create' => Pages\CreateGateEntry::route('/create'),
            'edit' => Pages\EditGateEntry::route('/{record}/edit'),
        ];
    }
}
