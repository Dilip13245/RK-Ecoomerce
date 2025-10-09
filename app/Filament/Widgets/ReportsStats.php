<?php

namespace App\Filament\Widgets;

use App\Models\{Order, Product, User, ProductReview};
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReportsStats extends BaseWidget
{
    protected function getStats(): array
    {
        $last30Days = Order::where('created_at', '>=', now()->subDays(30))->count();
        $avgOrderValue = Order::where('payment_status', 'paid')->avg('total_amount');
        $topRatedCount = Product::where('rating_average', '>=', 4.5)->count();
        $totalReviews = ProductReview::count();

        return [
            Stat::make('Orders (30 Days)', $last30Days)
                ->description('Last 30 days')
                ->color('success'),
            Stat::make('Avg Order Value', '₹' . number_format($avgOrderValue, 0))
                ->description('Average per order')
                ->color('info'),
            Stat::make('Top Rated Products', $topRatedCount)
                ->description('4.5+ star rating')
                ->color('warning'),
            Stat::make('Total Reviews', $totalReviews)
                ->description('Customer reviews')
                ->color('primary'),
        ];
    }
}
