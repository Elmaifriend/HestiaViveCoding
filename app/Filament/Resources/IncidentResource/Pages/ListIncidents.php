<?php

namespace App\Filament\Resources\IncidentResource\Pages;

use Filament\Actions;
use App\Models\Incident;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\IncidentResource;

class ListIncidents extends ListRecords
{
    protected static string $resource = IncidentResource::class;

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
                ->badge(Incident::count()),

            'open' => Tab::make('Abiertos')
                ->icon('heroicon-m-exclamation-circle')
                ->badge(Incident::where('status', 'open')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'open')),

            'in_progress' => Tab::make('En Proceso')
                ->icon('heroicon-m-arrow-path')
                ->badge(Incident::where('status', 'in_progress')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'in_progress')),

            'resolved' => Tab::make('Resueltos')
                ->icon('heroicon-m-check-circle')
                ->badge(Incident::where('status', 'resolved')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'resolved')),

            'closed' => Tab::make('Cerrados')
                ->icon('heroicon-m-lock-closed')
                ->badge(Incident::where('status', 'closed')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'closed')),
        ];
    }
}
