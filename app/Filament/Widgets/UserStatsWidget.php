<?php

namespace App\Filament\Widgets;

use App\Models\{User, Product};
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $newUsersToday = User::whereDate('created_at', today())->count();
        $activeProducts = Product::where('is_active', true)->where('is_deleted', false)->count();
        $totalCategories = \App\Models\Category::where('is_active', true)->count();
        $activeCoupons = \App\Models\Coupon::where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->count();

        return [
            Stat::make('New Users Today', $newUsersToday)
                ->description('Registered today')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success'),
            Stat::make('Active Products', $activeProducts)
                ->description('Available for sale')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),
            Stat::make('Categories', $totalCategories)
                ->description('Product categories')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning'),
            Stat::make('Active Coupons', $activeCoupons)
                ->description('Currently valid')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
        ];
    }
}
