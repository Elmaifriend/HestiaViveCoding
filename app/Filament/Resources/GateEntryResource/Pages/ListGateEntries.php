<?php

namespace App\Filament\Resources\GateEntryResource\Pages;

use Filament\Actions;
use App\Models\GateEntry;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\GateEntryResource;

class ListGateEntries extends ListRecords
{
    protected static string $resource = GateEntryResource::class;

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
                ->badge(GateEntry::count()),

            'pending' => Tab::make('Pendientes')
                ->icon('heroicon-m-clock')
                ->badge(GateEntry::where('status', 'pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

            'approved' => Tab::make('Aprobados')
                ->icon('heroicon-m-check-circle')
                ->badge(GateEntry::where('status', 'approved')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved')),

            'denied' => Tab::make('Denegados')
                ->icon('heroicon-m-no-symbol')
                ->badge(GateEntry::where('status', 'denied')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'denied')),
        ];
    }
}
