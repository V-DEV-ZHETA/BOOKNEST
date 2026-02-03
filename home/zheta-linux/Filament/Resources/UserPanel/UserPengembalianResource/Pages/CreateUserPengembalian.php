<?php

namespace App\Filament\Resources\UserPengembalianResource\Pages;

use App\Filament\Resources\UserPengembalianResource;
use App\Models\Peminjaman;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUserPengembalian extends CreateRecord
{
    protected static string $resource = UserPengembalianResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['return_date'] = now();
        $data['confirmation_status'] = 'pending';
        
        // Validasi peminjaman
        $peminjaman = Peminjaman::find($data['peminjaman_id']);
        if (!$peminjaman) {
            $this->addError('peminjaman_id', 'Peminjaman tidak ditemukan.');
            return [];
        }
        
        // Validasi bahwa peminjaman milik user ini
        if ($peminjaman->user_id !== Auth::id()) {
            $this->addError('peminjaman_id', 'Peminjaman ini bukan milik Anda.');
            return [];
        }
        
        // Validasi status peminjaman
        if ($peminjaman->status !== 'borrowed' || $peminjaman->confirmation_status !== 'approved') {
            $this->addError('peminjaman_id', 'Peminjaman ini tidak dapat dikembalikan.');
            return [];
        }
        
        // Hitung keterlambatan
        $lateDays = now()->diffInDays($peminjaman->due_date, false);
        $data['late_days'] = $lateDays > 0 ? $lateDays : 0;
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Update status peminjaman menjadi 'returned' setelah staff mengkonfirmasi
        // Ini akan dilakukan oleh staff saat konfirmasi
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pengajuan Pengembalian Berhasil';
    }

    protected function getCreatedNotificationMessage(): ?string
    {
        return 'Pengajuan pengembalian Anda telah dikirim dan menunggu konfirmasi dari staff perpustakaan.';
    }
}

