<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Incident;
use App\Enums\IncidentStatus;

class IncidentsChart extends ChartWidget
{
    protected ?string $heading = 'Incidents by Status';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Incident::query()
             ->selectRaw('count(*) as count, status')
             ->groupBy('status')
             ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Incidents',
                    'data' => $data->pluck('count'),
                    'backgroundColor' => ['#ef4444', '#f59e0b', '#22c55e', '#6b7280'],
                ],
            ],
            'labels' => $data->map(fn ($row) => $row->status instanceof IncidentStatus ? $row->status->getLabel() : $row->status),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
