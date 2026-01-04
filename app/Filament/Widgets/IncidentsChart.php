<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Incident;
use App\Enums\IncidentStatus;

class IncidentsChart extends ChartWidget
{
    protected ?string $heading = 'Estado de los Incidentes';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Incident::query()
            ->selectRaw('count(*) as count, status')
            ->groupBy('status')
            ->get();

        $colors = [
            'open' => '#ef4444',
            'in_progress' => '#f59e0b',
            'resolved' => '#10b981',
            'closed' => '#6b7280',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Incidentes',
                    'data' => $data->pluck('count'),

                    'backgroundColor' => $data->map(fn($row) => $colors[$row->status->value] ?? '#9ca3af'),

                    'borderWidth' => 0,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $data->map(fn($row) => $row->status->getLabel()),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '75%',
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
