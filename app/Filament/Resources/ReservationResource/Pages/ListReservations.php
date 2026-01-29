<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use Filament\Actions;
use App\Models\Reservation;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ReservationResource;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas')
                ->icon('heroicon-m-list-bullet')
                ->badge(Reservation::count()),

            'pending' => Tab::make('Pendientes')
                ->icon('heroicon-m-clock')
                ->badge(Reservation::where('status', 'pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending')),

            'approved' => Tab::make('Confirmadas')
                ->icon('heroicon-m-check-circle')
                ->badge(Reservation::where('status', 'approved')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'approved')),

            'rejected' => Tab::make('Rechazadas')
                ->icon('heroicon-m-x-circle')
                ->badge(Reservation::where('status', 'rejected')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rejected')),

            'cancelled' => Tab::make('Canceladas')
                ->icon('heroicon-m-no-symbol')
                ->badge(Reservation::where('status', 'cancelled')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled')),
        ];
    }
}
