<?php

namespace App\Filament\Resources;

use App\Enums\GateEntryStatus;
use App\Filament\Resources\GateEntryResource\Pages;
use App\Models\GateEntry;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GateEntryResource extends Resource
{
    protected static ?string $model = GateEntry::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Accesos y Seguridad';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-key';

    protected static ?string $modelLabel = 'Entrada';

    protected static ?string $pluralModelLabel = 'Entradas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('resident_id')
                    ->relationship('resident', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('guest_name')
                    ->required(),
                Forms\Components\DateTimePicker::make('entry_date')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(GateEntryStatus::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guest_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('resident.name')
                    ->label('Resident')
                    ->searchable(),
                TextColumn::make('entry_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(GateEntryStatus::class),
            ])
            ->actions([
                //EditAction::make(),
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
            'index' => Pages\ListGateEntries::route('/'),
            'create' => Pages\CreateGateEntry::route('/create'),
            'edit' => Pages\EditGateEntry::route('/{record}/edit'),
        ];
    }
}
