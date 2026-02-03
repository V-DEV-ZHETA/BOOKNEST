<?php

namespace App\Filament\Resources\UserPeminjamanResource\Pages;

use App\Filament\Resources\UserPeminjamanResource;
use App\Models\InventoriBuku;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListUserPeminjamen extends ListRecords
{
    protected static string $resource = UserPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajukan Peminjaman')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn (Model $record) => route('filament.user.resources.user-peminjaman.view', ['record' => $record]);
    }
}

