<?php

namespace App\Filament\Widgets;

use App\Models\InventoriBuku;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalBooks = InventoriBuku::sum('quantity');
        $totalTitles = InventoriBuku::count();
        $activeLoans = Peminjaman::where('status', 'borrowed')
            ->where('confirmation_status', 'approved')
            ->count();
        $pendingReturns = Pengembalian::where('confirmation_status', 'pending')->count();
        $pendingConfirmations = Peminjaman::where('confirmation_status', 'pending')->count();
        $totalMembers = User::where('status', 'active')->count();
        $overdueLoans = Peminjaman::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->count();

        return [
            Stat::make('Total Buku', number_format($totalBooks))
                ->description('Jumlah buku keseluruhan')
                ->descriptionIcon('heroicon-o-book-open')
                ->color('primary'),
            
            Stat::make('Judul Buku', number_format($totalTitles))
                ->description('Jumlah judul unik')
                ->descriptionIcon('heroicon-o-identification')
                ->color('success'),
            
            Stat::make('Sedang Dipinjam', number_format($activeLoans))
                ->description('Peminjaman aktif')
                ->descriptionIcon('heroicon-o-arrow-up-right')
                ->color('info'),
            
            Stat::make('Menunggu Konfirmasi', number_format($pendingConfirmations))
                ->description('Permetujuan peminjaman')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),
            
            Stat::make('Pengembalian Pending', number_format($pendingReturns))
                ->description('Menunggu verifikasi')
                ->descriptionIcon('heroicon-o-arrow-uturn-left')
                ->color('danger'),
            
            Stat::make('Terlambat', number_format($overdueLoans))
                ->description('Lewat tanggal jatuh tempo')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger'),
            
            Stat::make('Total Anggota', number_format($totalMembers))
                ->description('Anggota aktif')
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),
        ];
    }
}

