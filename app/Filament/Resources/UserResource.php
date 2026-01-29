<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\UserResource\Pages;
use Filament\Schemas\Components\Utilities\Get;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Usuarios';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $pluralModelLabel = 'Usuarios del Sistema';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Personal')
                    ->description('Datos básicos e identificación del usuario.')
                    ->icon('heroicon-m-identification')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre Completo')
                            ->placeholder('Ej. Carlos Rodríguez')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-user'),

                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->placeholder('usuario@ejemplo.com')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-m-envelope'),

                        Select::make('role')
                            ->label('Rol Asignado')
                            ->options([
                                'admin' => 'Administrador',
                                'resident' => 'Residente',
                                'guard' => 'Guardia de Seguridad',
                            ])
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-m-shield-check'),
                    ])->columns(2),

                Section::make('Credenciales de Acceso')
                    ->description('Gestión de contraseña y seguridad de la cuenta.')
                    ->icon('heroicon-m-lock-closed')
                    ->collapsible()
                    ->schema([
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->confirmed()
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->prefixIcon('heroicon-m-key')
                            ->helperText('Deje este campo vacío si no desea cambiar la contraseña actual.')
                            ->columnSpan(1), // Ocupa la mitad

                        TextInput::make('password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->password()
                            ->revealable()
                            ->required(fn(Get $get) => filled($get('password')))
                            ->dehydrated(false)
                            ->prefixIcon('heroicon-m-check-badge')
                            ->placeholder('Repita la contraseña')
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user-circle')
                    ->description(fn(User $record) => $record->email),

                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state->value ?? $state) {
                        'admin' => 'Administrador',
                        'resident' => 'Residente',
                        'guard' => 'Guardia',
                        default => $state,
                    })
                    ->color(fn($state) => match ($state->value ?? $state) {
                        'admin' => 'danger',
                        'resident' => 'success',
                        'guard' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn($state) => match ($state->value ?? $state) {
                        'admin' => 'heroicon-m-shield-check',
                        'resident' => 'heroicon-m-home',
                        'guard' => 'heroicon-m-lock-closed',
                        default => null,
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('role')
                    ->label('Filtrar por Rol')
                    ->options([
                        'admin' => 'Administrador',
                        'resident' => 'Residente',
                        'guard' => 'Guardia',
                    ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
