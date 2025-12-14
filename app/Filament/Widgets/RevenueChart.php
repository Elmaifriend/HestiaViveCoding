<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Payment;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue (Last 12 Months)';
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
                    'label' => 'Revenue',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
