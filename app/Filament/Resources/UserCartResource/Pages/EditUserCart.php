<?php

namespace App\Filament\Resources\UserCartResource\Pages;

use App\Filament\Resources\UserCartResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserCart extends EditRecord
{
    protected static string $resource = UserCartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
