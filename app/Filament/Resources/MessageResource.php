<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Message;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\MessageResource\Pages;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Comunidad';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $modelLabel = 'Mensaje';

    protected static ?string $pluralModelLabel = 'Mensajería';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Intercambio de Mensajes')
                    ->columnSpanFull()
                    ->description('Detalles de la comunicación entre usuarios.')
                    ->icon('heroicon-m-envelope')
                    ->schema([
                        Forms\Components\Select::make('sender_id')
                            ->relationship('sender', 'name')
                            ->label('Remitente (De)')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-m-arrow-left-start-on-rectangle')
                            ->columnSpan(1),

                        Forms\Components\Select::make('receiver_id')
                            ->relationship('receiver', 'name')
                            ->label('Destinatario (Para)')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-m-arrow-right-end-on-rectangle')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('content')
                            ->label('Contenido del Mensaje')
                            ->placeholder('Escriba el mensaje aquí...')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\DateTimePicker::make('read_at')
                            ->label('Leído el')
                            ->helperText('Deje este campo vacío si el mensaje debe constar como "No Leído".')
                            ->native(false)
                            ->prefixIcon('heroicon-m-eye')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sender.name')
                    ->label('De')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-m-user-circle')
                    ->description(fn (Message $record) => $record->sender->email ?? ''),

                TextColumn::make('receiver.name')
                    ->label('Para')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-arrow-right')
                    ->color('gray'),

                TextColumn::make('content')
                    ->label('Mensaje')
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn (Message $record) => $record->content)
                    ->searchable(),

                TextColumn::make('read_at')
                    ->label('Estado')
                    ->badge()
                    ->getStateUsing(fn (Message $record) => $record->read_at ? 'Leído' : 'No leído')
                    ->color(fn (string $state) => $state === 'Leído' ? 'success' : 'warning')
                    ->icon(fn (string $state) => $state === 'Leído' ? 'heroicon-m-check-badge' : 'heroicon-m-envelope'),

                TextColumn::make('created_at')
                    ->label('Enviado')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('read_at')
                    ->label('Estado de Lectura')
                    ->placeholder('Todos los mensajes')
                    ->trueLabel('Solo Leídos')
                    ->falseLabel('Solo No Leídos')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('read_at'),
                        false: fn ($query) => $query->whereNull('read_at'),
                    ),
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
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
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }
}
