<?php

namespace App\Filament\Resources\InvitationResource\Pages;

use Filament\Actions;
use App\Models\Invitation;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\InvitationResource;

class ListInvitations extends ListRecords
{
    protected static string $resource = InvitationResource::class;

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
                ->badge(Invitation::count()),

            'active' => Tab::make('Activas')
                ->icon('heroicon-m-ticket')
                ->badge(Invitation::where('status', 'active')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active')),

            'used' => Tab::make('Usadas')
                ->icon('heroicon-m-check-badge')
                ->badge(Invitation::where('status', 'used')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'used')),

            'expired' => Tab::make('Vencidas')
                ->icon('heroicon-m-x-circle')
                ->badge(Invitation::where('status', 'expired')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'expired')),
        ];
    }
}
