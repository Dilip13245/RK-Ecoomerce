<?php

namespace App\Filament\Resources\UserCartResource\Pages;

use App\Filament\Resources\UserCartResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserCart extends CreateRecord
{
    protected static string $resource = UserCartResource::class;
}
