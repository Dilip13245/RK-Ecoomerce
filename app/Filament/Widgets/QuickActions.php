<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
}
