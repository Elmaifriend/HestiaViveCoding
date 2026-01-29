<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Payment;
use Filament\Tables\Table;
use App\Enums\PaymentStatus;
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
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\PaymentResource\Pages;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Finanzas';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $modelLabel = 'Pago';

    protected static ?string $pluralModelLabel = 'Historial de Pagos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la Transacción')
                    ->columnSpanFull()
                    ->description('Registre la información del pago recibido.')
                    ->icon('heroicon-m-currency-dollar')
                    ->schema([
                        Forms\Components\Select::make('resident_id')
                            ->relationship('resident', 'name')
                            ->label('Residente')
                            ->placeholder('Buscar residente...')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->prefixIcon('heroicon-m-user'),

                        Forms\Components\TextInput::make('amount')
                            ->label('Monto Pagado')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->required(),

                        Forms\Components\DateTimePicker::make('date_paid')
                            ->label('Fecha de Pago')
                            ->required()
                            ->native(false)
                            ->maxDate(now())
                            ->default(now())
                            ->prefixIcon('heroicon-m-calendar'),
                    ])->columns(3),

                Section::make('Comprobante y Validación')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\FileUpload::make('receipt_url')
                            ->label('Comprobante / Recibo')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('payments')
                            ->visibility('public')
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),

                        Group::make()
                            ->schema([
                                ToggleButtons::make('status')
                                    ->label('')
                                    ->options([
                                        'pending' => 'Pendiente',
                                        'approved' => 'Aprobado',
                                        'rejected' => 'Rechazado',
                                    ])
                                    ->required()
                                    ->inline()
                                    ->grouped()
                                    ->default('pending')
                                    ->icons([
                                        'pending' => 'heroicon-m-clock',
                                        'approved' => 'heroicon-m-check-circle',
                                        'rejected' => 'heroicon-m-x-circle',
                                    ])
                                    ->colors([
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    ])
                            ])
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'flex justify-center w-full']),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('resident.name')
                    ->label('Pagador')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->description(fn(Payment $record) => $record->resident->email),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->size('lg'),

                TextColumn::make('date_paid')
                    ->label('Fecha')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray'),

                ImageColumn::make('receipt_url')
                    ->label('Recibo')
                    ->disk('public')
                    ->visibility('public'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state->value ?? $state) {
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        default => $state,
                    })
                    ->color(fn($state) => match ($state->value ?? $state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn($state) => match ($state->value ?? $state) {
                        'pending' => 'heroicon-m-clock',
                        'approved' => 'heroicon-m-check-circle',
                        'rejected' => 'heroicon-m-x-circle',
                        default => null,
                    })
                    ->sortable(),
            ])
            ->defaultSort('date_paid', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado del Pago')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                    ]),

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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
