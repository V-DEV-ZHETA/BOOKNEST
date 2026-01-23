<?php

namespace App\Filament\Resources\InventoriBukuResource\Pages;

use App\Filament\Resources\InventoriBukuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventoriBuku extends EditRecord
{
    protected static string $resource = InventoriBukuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
