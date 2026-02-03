<?php

namespace App\Filament\Resources\UserPeminjamanResource\Pages;

use App\Filament\Resources\UserPeminjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserPeminjaman extends ViewRecord
{
    protected static string $resource = UserPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->hidden(),
            Actions\Action::make('kembalikan')
                ->label('Kembalikan Buku')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('success')
                ->visible(fn () => $this->record->confirmation_status === 'approved' && $this->record->status === 'borrowed')
                ->url(fn () => route('filament.user.resources.user-pengembalians.create', ['peminjaman_id' => $this->record->id])),
        ];
    }
}

