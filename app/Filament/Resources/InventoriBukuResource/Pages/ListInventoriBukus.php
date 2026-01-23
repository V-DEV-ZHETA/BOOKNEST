<?php

namespace App\Filament\Resources\InventoriBukuResource\Pages;

use App\Filament\Resources\InventoriBukuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventoriBukus extends ListRecords
{
    protected static string $resource = InventoriBukuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
