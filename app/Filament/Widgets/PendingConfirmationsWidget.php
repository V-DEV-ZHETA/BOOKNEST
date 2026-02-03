<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingConfirmationsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Peminjaman Menunggu Konfirmasi')
            ->description('Perlu tindakan dari staff')
            ->paginated(false)
            ->defaultSort('created_at', 'desc')
            ->query(
                Peminjaman::with(['user', 'inventoriBuku'])
                    ->where('confirmation_status', 'pending')
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
                    ->formatStateUsing(fn ($state, $record) => $state ?? '-')
                    ->tooltip(fn ($record) => $record->user?->email),
                
                TextColumn::make('inventoriBuku.title')
                    ->label('Buku')
                    ->searchable()
                    ->wrap()
                    ->formatStateUsing(fn ($state, $record) => $state ?? '-'),
                
                TextColumn::make('borrow_date')
                    ->label('Tgl Pinjam')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since(),
            ])
            ->emptyStateHeading('Tidak ada peminjaman')
            ->emptyStateDescription('Semua peminjaman telah diproses')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}

