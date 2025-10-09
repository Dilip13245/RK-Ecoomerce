<?php

namespace App\Filament\Resources\HelpSupportResource\Pages;

use App\Filament\Resources\HelpSupportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHelpSupport extends EditRecord
{
    protected static string $resource = HelpSupportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
