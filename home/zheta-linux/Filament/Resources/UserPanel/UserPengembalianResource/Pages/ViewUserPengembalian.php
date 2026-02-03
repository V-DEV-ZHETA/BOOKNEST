<?php

namespace App\Filament\Resources\UserPengembalianResource\Pages;

use App\Filament\Resources\UserPengembalianResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserPengembalian extends ViewRecord
{
    protected static string $resource = UserPengembalianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->hidden(),
        ];
    }
}

