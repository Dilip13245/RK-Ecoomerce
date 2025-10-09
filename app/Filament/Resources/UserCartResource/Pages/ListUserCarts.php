<?php

namespace App\Filament\Resources\UserCartResource\Pages;

use App\Filament\Resources\UserCartResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserCarts extends ListRecords
{
    protected static string $resource = UserCartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
