<?php

namespace App\Filament\Resources;

use App\Enums\PaymentStatus;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Finanzas';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $modelLabel = 'Pago';

    protected static ?string $pluralModelLabel = 'Pagos';
// ...

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('resident.name')
                    ->label('Resident')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('amount')
                    ->money()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('date_paid')
                    ->dateTime()
                    ->sortable(),
                ImageColumn::make('receipt_url')
                    ->label('Receipt')
                    ->disk('public'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(PaymentStatus::class),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
