<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Incident;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Group;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\IncidentResource\Pages;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Comunidad';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $modelLabel = 'Incidente';

    protected static ?string $pluralModelLabel = 'Reportes e Incidentes';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Detalles del Reporte')
                            ->description('Información descriptiva del problema reportado.')
                            ->icon('heroicon-m-clipboard-document-list')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Asunto / Título')
                                    ->placeholder('Ej. Fuga de agua en pasillo norte')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('description')
                                    ->label('Descripción Detallada')
                                    ->placeholder('Explique la situación, ubicación exacta y otros detalles...')
                                    ->rows(5)
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Evidencia')
                            ->icon('heroicon-m-camera')
                            ->collapsible()
                            ->schema([
                                Forms\Components\FileUpload::make('photo_url')
                                    ->label('Fotografía Adjunta')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('incidents')
                                    ->visibility('public')
                                    ->disk('public')
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Estado y Seguimiento')
                            ->icon('heroicon-m-adjustments-horizontal')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Estado Actual')
                                    ->options([
                                        'open' => 'Abierto / Pendiente',
                                        'in_progress' => 'En Proceso',
                                        'resolved' => 'Resuelto',
                                        'closed' => 'Cerrado',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->prefixIcon('heroicon-m-signal'),

                                Forms\Components\Select::make('resident_id')
                                    ->relationship('resident', 'name')
                                    ->label('Reportado Por')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->prefixIcon('heroicon-m-user')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')->required(),
                                        Forms\Components\TextInput::make('email')->email()->required(),
                                    ]),

                                Placeholder::make('created_at')
                                    ->label('Fecha de Reporte')
                                    ->content(fn(Incident $record): string => $record->created_at?->format('d M Y, h:i A') ?? '-')
                                    ->hiddenOn('create'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_url')
                    ->label('Evidencia')
                    ->disk('public')
                    ->visibility('public')
                    ->imageHeight(120),

                TextColumn::make('title')
                    ->label('Incidente')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->description(fn(Incident $record) => Str::limit($record->description, 60)),

                TextColumn::make('resident.name')
                    ->label('Reportado por')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state->value ?? $state) {
                        'open' => 'Abierto',
                        'in_progress' => 'En Proceso',
                        'resolved' => 'Resuelto',
                        'closed' => 'Cerrado',
                        default => $state,
                    })
                    ->color(fn($state) => match ($state->value ?? $state) {
                        'open' => 'danger',
                        'in_progress' => 'info',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn($state) => match ($state->value ?? $state) {
                        'open' => 'heroicon-m-exclamation-circle',
                        'in_progress' => 'heroicon-m-arrow-path',
                        'resolved' => 'heroicon-m-check-circle',
                        'closed' => 'heroicon-m-lock-closed',
                        default => null,
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Filtrar por Estado')
                    ->options([
                        'open' => 'Abierto',
                        'in_progress' => 'En Proceso',
                        'resolved' => 'Resuelto',
                        'closed' => 'Cerrado',
                    ]),

                SelectFilter::make('resident')
                    ->label('Por Residente')
                    ->relationship('resident', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
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
            'index' => Pages\ListIncidents::route('/'),
            'create' => Pages\CreateIncident::route('/create'),
            'edit' => Pages\EditIncident::route('/{record}/edit'),
        ];
    }
}
