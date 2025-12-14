<?php

namespace App\Filament\Resources;

use App\Enums\InvitationStatus;
use App\Filament\Resources\InvitationResource\Pages;
use App\Models\Invitation;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Accesos y Seguridad';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $modelLabel = 'Invitación';

    protected static ?string $pluralModelLabel = 'Invitaciones';
// ...

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('resident.name')
                    ->label('Resident')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('qr_code')
                    ->searchable(),
                TextColumn::make('expiration_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(InvitationStatus::class),
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
            'index' => Pages\ListInvitations::route('/'),
            'create' => Pages\CreateInvitation::route('/create'),
            'edit' => Pages\EditInvitation::route('/{record}/edit'),
        ];
    }
}
