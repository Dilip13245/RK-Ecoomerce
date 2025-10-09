<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';
    protected function getStats(): array
    {
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total_amount');
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $lowStock = ProductColor::where('stock', '<=', 10)->where('stock', '>', 0)->count();
        
        return [
            Stat::make('Today Orders', $todayOrders)
                ->description('Orders placed today')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart([7, 12, 8, 15, 10, 18, $todayOrders])
                ->color('success'),
            Stat::make('Today Revenue', '₹' . number_format($todayRevenue, 0))
                ->description('Revenue earned today')
                ->descriptionIcon('heroicon-m-currency-rupee')
                ->chart([5000, 8000, 6000, 12000, 9000, 15000, $todayRevenue])
                ->color('info'),
            Stat::make('Pending Orders', $pendingOrders)
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Low Stock Items', $lowStock)
                ->description('Products need restock')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make('Total Users', User::count())
                ->description(User::where('user_type', 'customer')->count() . ' customers, ' . User::where('user_type', 'seller')->count() . ' sellers')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Total Revenue', '₹' . number_format(Order::where('payment_status', 'paid')->sum('total_amount'), 0))
                ->description('All time sales')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
