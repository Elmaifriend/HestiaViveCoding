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
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            // ESTADÍSTICA 1: INGRESOS
            Stat::make('Ingresos Totales', '$' . number_format(Payment::sum('amount'), 2))
                ->description('Ingresos históricos acumulados')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart($this->getTrendData(Payment::class, 'sum', 'amount', 'date_paid')) // Chart real basado en fecha de pago
                ->color('success'),

            // ESTADÍSTICA 2: RESIDENTES
            Stat::make('Residentes Activos', User::where('role', UserRole::Resident)->count())
                ->description('Total de residentes registrados')
                ->descriptionIcon('heroicon-m-users')
                ->chart($this->getTrendData(User::class, 'count')) // Muestra nuevos registros
                ->color('primary'),

            // ESTADÍSTICA 3: INCIDENTES
            Stat::make('Incidentes Abiertos', Incident::where('status', IncidentStatus::Open)->count())
                ->description('Requieren atención inmediata')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->chart($this->getTrendData(Incident::class, 'count')) // Muestra volumen de incidentes recientes
                ->color('danger'),

            // ESTADÍSTICA 4: ACCESOS
            Stat::make('Accesos Pendientes', GateEntry::where('status', GateEntryStatus::Pending)->count())
                ->description('Solicitudes de visita por aprobar')
                ->descriptionIcon('heroicon-m-qr-code') // Icono más acorde a accesos/códigos
                ->chart($this->getTrendData(GateEntry::class, 'count'))
                ->color('warning'),
        ];
    }

    /**
     * Función auxiliar para generar datos del gráfico (Sparkline)
     * Obtiene los datos de los últimos 7 días.
     */
    protected function getTrendData(string $model, string $aggregateType, string $column = 'id', string $dateColumn = 'created_at'): array
    {
        try {
            $trend = Trend::model($model)
                ->dateColumn($dateColumn)
                ->between(
                    start: now()->subDays(7),
                    end: now(),
                )
                ->perDay();

            if ($aggregateType === 'sum') {
                $data = $trend->sum($column);
            } else {
                $data = $trend->count();
            }

            return $data->map(fn (TrendValue $value) => $value->aggregate)->toArray();

        } catch (\Exception $e) {
            // Fallback silencioso: si falla el cálculo (ej. falta tabla), devuelve array vacío para no romper el widget
            return [];
        }
    }
}
