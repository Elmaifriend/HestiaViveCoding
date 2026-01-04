<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\Announcement;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Group;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\AnnouncementResource\Pages;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Comunidad';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $modelLabel = 'Anuncio';

    protected static ?string $pluralModelLabel = 'Tablón de Anuncios';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Contenido del Comunicado')
                            ->description('Redacte la información que verán los residentes.')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Título del Anuncio')
                                    ->placeholder('Ej. Mantenimiento programado de elevadores')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true),

                                Forms\Components\RichEditor::make('content')
                                    ->label('Cuerpo del Mensaje')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'bulletList',
                                        'orderedList',
                                        'link',
                                        'h2',
                                        'h3'
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Configuración')
                            ->icon('heroicon-m-cog-6-tooth')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Tipo de Aviso')
                                    ->options([
                                        'news' => 'Noticias',
                                        'alert' => 'Alerta Urgente',
                                        'maintenance' => 'Mantenimiento',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->prefixIcon('heroicon-m-tag'),

                                Forms\Components\Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'draft' => 'Borrador',
                                        'published' => 'Publicado',
                                        'archived' => 'Archivado',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->prefixIcon('heroicon-m-signal'),

                                Section::make('Notificaciones')
                                    ->schema([
                                        Forms\Components\Toggle::make('send_push')
                                            ->label('Enviar Push')
                                            ->helperText('Notificar a los dispositivos móviles de los residentes.')
                                            ->onColor('success')
                                            ->offColor('gray')
                                            ->default(false),
                                    ])
                                    ->compact(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->description(fn(Announcement $record) => Str::limit(strip_tags($record->content), 80)),

                // --- TRADUCCIÓN VISUAL DE TIPO ---
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state->value ?? $state) {
                        'news' => 'Noticias',
                        'alert' => 'Alerta',
                        'maintenance' => 'Mantenimiento',
                        default => $state,
                    })
                    ->color(fn($state) => match ($state->value ?? $state) {
                        'news' => 'info',
                        'alert' => 'danger',
                        'maintenance' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn($state) => match ($state->value ?? $state) {
                        'news' => 'heroicon-m-newspaper',
                        'alert' => 'heroicon-m-exclamation-triangle',
                        'maintenance' => 'heroicon-m-wrench-screwdriver',
                        default => null,
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state->value ?? $state) {
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                        'archived' => 'Archivado',
                        default => $state,
                    })
                    ->color(fn($state) => match ($state->value ?? $state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'archived' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn($state) => match ($state->value ?? $state) {
                        'draft' => 'heroicon-m-pencil-square',
                        'published' => 'heroicon-m-check-badge',
                        'archived' => 'heroicon-m-archive-box',
                        default => null,
                    })
                    ->sortable(),

                IconColumn::make('send_push')
                    ->label('Notificado')
                    ->boolean()
                    ->trueIcon('heroicon-o-bell-alert')
                    ->falseIcon('heroicon-o-bell-slash')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Publicado')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Filtrar por Tipo')
                    ->options([
                        'news' => 'Noticias',
                        'alert' => 'Alerta',
                        'maintenance' => 'Mantenimiento',
                    ]),
                SelectFilter::make('status')
                    ->label('Filtrar por Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                        'archived' => 'Archivado',
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
