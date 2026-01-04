<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use Filament\Actions;
use App\Models\Announcement;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AnnouncementResource;

class ListAnnouncements extends ListRecords
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos')
                ->icon('heroicon-m-list-bullet')
                ->badge(Announcement::count()),

            'news' => Tab::make('Noticias')
                ->icon('heroicon-m-newspaper')
                ->badge(Announcement::where('type', 'news')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'news')),

            'alert' => Tab::make('Alertas')
                ->icon('heroicon-m-exclamation-triangle')
                ->badge(Announcement::where('type', 'alert')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'alert')),

            'maintenance' => Tab::make('Mantenimiento')
                ->icon('heroicon-m-wrench-screwdriver')
                ->badge(Announcement::where('type', 'maintenance')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'maintenance')),
        ];
    }
}
