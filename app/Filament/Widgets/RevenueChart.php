<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Historial de Ingresos (Último Año)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Trend::model(Payment::class)
            ->dateColumn('date_paid')
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Ingresos Recaudados',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),

                    'fill' => 'start',
                    'tension' => 0.4,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'pointBackgroundColor' => '#ffffff',
                    'pointBorderColor' => '#10b981',
                    'pointHoverBackgroundColor' => '#10b981',
                    'pointHoverBorderColor' => '#ffffff',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M Y')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
