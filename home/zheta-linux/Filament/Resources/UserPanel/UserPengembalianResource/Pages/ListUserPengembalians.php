<?php

namespace App\Filament\Resources\UserPengembalianResource\Pages;

use App\Filament\Resources\UserPengembalianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserPengembalians extends ListRecords
{
    protected static string $resource = UserPengembalianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Create action only visible if peminjaman_id is provided in query
            Actions\Action::make('kembalikan')
                ->label('Ajukan Pengembalian')
                ->icon('heroicon-o-plus')
                ->url(fn () => route('filament.user.resources.user-peminjaman.index'))
                ->color('primary'),
        ];
    }

    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record) => route('filament.user.resources.user-pengembalians.view', ['record' => $record]);
    }
}

