<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 99;
    protected static string $view = 'filament.pages.reports';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\ReportsStats::class,
        ];
    }
}
