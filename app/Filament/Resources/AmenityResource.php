<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AmenityResource\Pages;
use App\Models\Amenity;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AmenityResource extends Resource
{
    protected static ?string $model = Amenity::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Instalaciones y Reservas';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $modelLabel = 'Amenidad';

    protected static ?string $pluralModelLabel = 'Amenidades';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->description('Basic details about the amenity.')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        TextInput::make('location')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        TextInput::make('capacity')
                            ->numeric()
                            ->default(10)
                            ->required()
                            ->columnSpan(1),
                    ])->columns(3),

                Section::make('Additional Details')
                    ->schema([
                        Textarea::make('description')
                            ->columnSpanFull(),
                        FileUpload::make('photo_url')
                            ->label('Photo')
                            ->image()
                            ->directory('amenities')
                            ->visibility('public')
                            ->openable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('location')
                    ->searchable()
                    ->icon('heroicon-m-map-pin'),
                TextColumn::make('capacity')
                    ->numeric()
                    ->sortable()
                    ->badge(),
                ImageColumn::make('photo_url')
                    ->label('Photo')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
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
            'index' => Pages\ListAmenities::route('/'),
            'create' => Pages\CreateAmenity::route('/create'),
            'edit' => Pages\EditAmenity::route('/{record}/edit'),
        ];
    }
}
