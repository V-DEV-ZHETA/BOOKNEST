<?php

namespace App\Filament\Resources\UserPeminjamanResource\Pages;

use App\Filament\Resources\UserPeminjamanResource;
use App\Models\InventoriBuku;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUserPeminjaman extends CreateRecord
{
    protected static string $resource = UserPeminjamanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['borrow_date'] = now();
        $data['status'] = 'borrowed';
        $data['confirmation_status'] = 'pending';
        
        // Validasi stok buku
        $book = InventoriBuku::find($data['inventori_buku_id']);
        if (!$book || $book->quantity <= 0) {
            $this->addError('inventori_buku_id', 'Buku tidak tersedia untuk dipinjam.');
            return [];
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Tidak perlu mengubah stok di sini, karena baru setelah dikonfirmasi staff
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pengajuan Peminjaman Berhasil';
    }

    protected function getCreatedNotificationMessage(): ?string
    {
        return 'Pengajuan peminjaman Anda telah dikirim dan menunggu konfirmasi dari staff perpustakaan.';
    }
}

