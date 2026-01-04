<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Invitation;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Enums\InvitationStatus;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Support\Enums\FontFamily;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Set;
use App\Filament\Resources\InvitationResource\Pages;

class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Accesos y Seguridad';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $modelLabel = 'Invitación QR';

    protected static ?string $pluralModelLabel = 'Invitaciones QR';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del Pase')
                    ->description('Información técnica de acceso.')
                    ->icon('heroicon-m-qr-code')
                    ->schema([
                        Forms\Components\Select::make('resident_id')
                            ->relationship('resident', 'name')
                            ->label('Residente Anfitrión')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('expiration_date')
                            ->label('Válido Hasta')
                            ->required()
                            ->native(false)
                            ->seconds(false)
                            ->minDate(now())
                            ->default(now()->addDay())
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('qr_code')
                            ->label('Código Generado')
                            ->default(fn() => Str::random(10))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->suffixAction(
                                Action::make('regenerate')
                                    ->icon('heroicon-m-arrow-path')
                                    ->action(fn(Set $set) => $set('qr_code', Str::random(10)))
                            )
                            ->columnSpanFull(),

                        ToggleButtons::make('status')
                            ->options(InvitationStatus::class)
                            ->required()
                            ->inline()
                            ->grouped()
                            ->options([
                                'active' => 'Activa',
                                'used' => 'Usada',
                                'expired' => 'Vencida',
                            ])
                            ->default('active')
                    ])->columns(2),

                Section::make('Información del Evento')
                    ->description('Detalles sobre la visita y acompañantes.')
                    ->icon('heroicon-m-users')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Motivo de la Visita')
                            ->placeholder('Ej. Instalación de Internet, Cena familiar...')
                            ->autoSize()
                            ->columnSpanFull(),

                        Forms\Components\TagsInput::make('guest_names')
                            ->label('Lista de Invitados')
                            ->placeholder('Escribe un nombre y presiona Enter')
                            ->helperText('Ingrese los nombres completos de las personas autorizadas con este código.')
                            ->separator(',')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('resident.name')
                    ->label('Anfitrión')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->description(fn(Invitation $record) => $record->resident->email),

                TextColumn::make('guest_names')
                    ->label('Invitados')
                    ->badge()
                    ->separator(',')
                    ->limitList(3)
                    ->searchable(),

                TextColumn::make('qr_code')
                    ->label('Código de Acceso')
                    ->fontFamily(FontFamily::Mono)
                    ->copyable()
                    ->copyMessage('Código copiado')
                    ->searchable()
                    ->color('primary'),

                TextColumn::make('expiration_date')
                    ->label('Vencimiento')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->color(fn(Invitation $record) => $record->expiration_date->isPast() ? 'danger' : 'gray')
                    ->icon(fn(Invitation $record) => $record->expiration_date->isPast() ? 'heroicon-m-exclamation-circle' : 'heroicon-m-clock'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state): string => match ($state->value) {
                        'active' => 'Activa',
                        'used' => 'Usada',
                        'expired' => 'Vencida',
                        default => $state->value,
                    })
                    ->color(fn($state): string => match ($state->value) {
                        'active' => 'success',
                        'used' => 'gray',
                        'expired' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn($state): ?string => match ($state->value ?? $state) {
                        'active' => 'heroicon-m-check-circle',
                        'used' => 'heroicon-m-information-circle',
                        'expired' => 'heroicon-m-x-circle',
                        default => null,
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Por Estado')
                    ->options(InvitationStatus::class),

                SelectFilter::make('resident')
                    ->label('Por Residente')
                    ->relationship('resident', 'name'),
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
            'index' => Pages\ListInvitations::route('/'),
            'create' => Pages\CreateInvitation::route('/create'),
            'edit' => Pages\EditInvitation::route('/{record}/edit'),
        ];
    }
}
