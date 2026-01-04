<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Tables\Table;
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
use App\Filament\Resources\ProductResource\Pages;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Comunidad';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $modelLabel = 'Producto';

    protected static ?string $pluralModelLabel = 'Marketplace';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Información del Artículo')
                            ->description('Describa el producto que se ofrece a la comunidad.')
                            ->icon('heroicon-m-tag')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Nombre del Producto')
                                    ->placeholder('Ej. Bicicleta de montaña, Sofá cama...')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('description')
                                    ->label('Descripción y Estado')
                                    ->placeholder('Detalles sobre la condición del artículo, tiempo de uso, etc.')
                                    ->rows(4)
                                    ->columnSpanFull(),

                            ])->columns(2),

                        Section::make('Galería')
                            ->schema([
                                Forms\Components\FileUpload::make('photo_url')
                                    ->label('Fotografía Principal')
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->directory('products')
                                    ->visibility('public')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Datos de Venta')
                            ->icon('heroicon-m-user')
                            ->schema([
                                Forms\Components\Select::make('resident_id')
                                    ->relationship('resident', 'name')
                                    ->label('Vendedor')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->prefixIcon('heroicon-m-user-circle'),

                                ToggleButtons::make('status')
                                    ->label('Disponibilidad')
                                    ->options([
                                        'active' => 'En Venta',
                                        'sold' => 'Vendido',
                                    ])
                                    ->required()
                                    ->inline()
                                    ->grouped()
                                    ->default('active')
                                    ->icons([
                                        'active' => 'heroicon-m-check',
                                        'sold' => 'heroicon-m-archive-box',
                                    ])
                                    ->colors([
                                        'active' => 'success',
                                        'sold' => 'gray',
                                    ]),

                                Forms\Components\TextInput::make('price')
                                    ->label('Precio de Venta')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->columnSpan(1),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_url')
                    ->label('Foto')
                    ->disk('public')
                    ->imageHeight(120),

                TextColumn::make('title')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Product $record) => \Illuminate\Support\Str::limit($record->description, 50)),

                TextColumn::make('price')
                    ->label('Precio')
                    ->money('USD')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('resident.name')
                    ->label('Vendedor')
                    ->icon('heroicon-m-user')
                    ->searchable()
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state->value ?? $state) {
                        'active' => 'Disponible',
                        'sold' => 'Vendido',
                        default => $state,
                    })
                    ->color(fn($state) => match ($state->value ?? $state) {
                        'active' => 'success',
                        'sold' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn($state) => match ($state->value ?? $state) {
                        'active' => 'heroicon-m-check-circle',
                        'sold' => 'heroicon-m-lock-closed',
                        default => null,
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Publicado')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'En Venta',
                        'sold' => 'Vendidos',
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
