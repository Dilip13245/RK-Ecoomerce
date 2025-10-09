<?php

namespace App\Filament\Resources\UserWishlistResource\Pages;

use App\Filament\Resources\UserWishlistResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserWishlist extends CreateRecord
{
    protected static string $resource = UserWishlistResource::class;
}
