<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrderStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Orders by Status';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '60s';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $statuses = Order::selectRaw('order_status, COUNT(*) as count')
            ->groupBy('order_status')
            ->pluck('count', 'order_status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Orders',
                    'data' => array_values($statuses),
                    'backgroundColor' => [
                        'rgba(251, 191, 36, 0.8)',  // pending - yellow
                        'rgba(96, 165, 250, 0.8)',  // processing - blue
                        'rgba(167, 139, 250, 0.8)', // shipped - purple
                        'rgba(52, 211, 153, 0.8)',  // delivered - green
                        'rgba(248, 113, 113, 0.8)', // cancelled - red
                    ],
                    'borderColor' => [
                        'rgb(251, 191, 36)',
                        'rgb(96, 165, 250)',
                        'rgb(167, 139, 250)',
                        'rgb(52, 211, 153)',
                        'rgb(248, 113, 113)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => array_map('ucfirst', array_keys($statuses)),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
