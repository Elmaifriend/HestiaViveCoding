<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Payment;
use App\Models\User;
use App\Models\Incident;
use App\Models\GateEntry;
use App\Enums\UserRole;
use App\Enums\IncidentStatus;
use App\Enums\GateEntryStatus;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Revenue', '$' . number_format(Payment::sum('amount'), 2))
                ->description('All time revenue')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Active Residents', User::where('role', UserRole::Resident)->count())
                ->description('Total registered residents')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Open Incidents', Incident::where('status', IncidentStatus::Open)->count())
                ->description('Requires attention')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make('Pending Gate Access', GateEntry::where('status', GateEntryStatus::Pending)->count())
                ->description('Visitor requests')
                ->descriptionIcon('heroicon-m-key')
                ->color('warning'),
        ];
    }
}
