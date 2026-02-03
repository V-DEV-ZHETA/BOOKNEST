<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTransactionsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Transaksi Terbaru')
            ->description('Peminjaman dan pengembalian terbaru')
            ->paginated(false)
            ->defaultSort('created_at', 'desc')
            ->query(
                Peminjaman::with(['user', 'inventoriBuku'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('No.')
                    ->width('50px')
                    ->alignCenter()
                    ->sortable(),
                
                TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        return $state ?: '-';
                    })
                    ->tooltip(function ($record) {
                        return $record->user?->email;
                    }),
                
                TextColumn::make('inventoriBuku.title')
                    ->label('Buku')
                    ->searchable()
                    ->wrap()
                    ->formatStateUsing(function ($state, $record) {
                        return $state ?: '-';
                    }),
                
                TextColumn::make('borrow_date')
                    ->label('Tgl Pinjam')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d/m/Y')
                    ->color(function ($record) {
                        return $record->isOverdue() ? 'danger' : null;
                    }),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'borrowed' => 'info',
                            'returned' => 'success',
                            'overdue' => 'danger',
                            'lost' => 'warning',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'borrowed' => 'Dipinjam',
                            'returned' => 'Dikembalikan',
                            'overdue' => 'Terlambat',
                            'lost' => 'Hilang',
                            default => $state,
                        };
                    }),
                
                TextColumn::make('confirmation_status')
                    ->label('Konfirmasi')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'pending' => 'Menunggu',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                            default => $state,
                        };
                    }),
            ]);
    }
}
