<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Filament\Resources\PeminjamanResource\RelationManagers;
use App\Models\InventoriBuku;
use App\Models\Peminjaman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    
    protected static ?string $navigationLabel = 'Peminjaman';

    protected static ?string $pluralModelLabel = 'Peminjaman';

    protected static ?string $modelLabel = 'Peminjaman Buku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Peminjam & Buku')
                    ->description('Pilih peminjam dan buku yang akan dipinjamkan')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->label('Peminjam')
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} ({$record->email})")
                            ->helperText('Pilih anggota yang akan meminjam buku')
                            ->searchPrompt('Cari berdasarkan nama atau email...')
                            ->placeholder('Pilih peminjam...'),
                        Forms\Components\Select::make('inventori_buku_id')
                            ->relationship('inventoriBuku', 'title')
                            ->required()
                            ->label('Buku')
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title} - {$record->author} (Stok: {$record->quantity})")
                            ->helperText('Pilih buku yang akan dipinjamkan')
                            ->searchPrompt('Cari berdasarkan judul atau pengarang...')
                            ->placeholder('Pilih buku...')
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Tanggal Peminjaman')
                    ->description('Atur tanggal pinjam dan jatuh tempo')
                    ->schema([
                        Forms\Components\DatePicker::make('borrow_date')
                            ->required()
                            ->label('Tanggal Pinjam')
                            ->default(now())
                            ->helperText('Tanggal buku dipinjam')
                            ->prefixIcon('heroicon-o-calendar')
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Forms\Components\DatePicker::make('due_date')
                            ->required()
                            ->label('Tanggal Jatuh Tempo')
                            ->helperText('Tanggal maksimal buku harus dikembalikan')
                            ->prefixIcon('heroicon-o-clock')
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Forms\Components\DatePicker::make('return_date')
                            ->label('Tanggal Pengembalian')
                            ->helperText('Diisi saat buku dikembalikan (kosong jika belum dikembalikan)')
                            ->prefixIcon('heroicon-o-check-circle')
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Status Peminjaman')
                    ->description('Status dan konfirmasi peminjaman')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'borrowed' => 'Dipinjam',
                                'returned' => 'Dikembalikan',
                                'overdue' => 'Terlambat',
                                'lost' => 'Hilang',
                            ])
                            ->required()
                            ->label('Status')
                            ->default('borrowed')
                            ->helperText('Status peminjaman buku'),
                        Forms\Components\Select::make('confirmation_status')
                            ->options([
                                'pending' => 'Menunggu Konfirmasi',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->required()
                            ->label('Status Konfirmasi')
                            ->default('pending')
                            ->disabled()
                            ->helperText('Status verifikasi oleh staff'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Konfirmasi Staff')
                    ->description('Informasi konfirmasi dari staff')
                    ->visible(fn ($record) => $record && $record->confirmation_status !== 'pending')
                    ->schema([
                        Forms\Components\TextInput::make('confirmer.name')
                            ->label('Dikonfirmasi Oleh')
                            ->disabled(),
                        Forms\Components\DateTime::make('confirmed_at')
                            ->label('Tanggal Konfirmasi')
                            ->disabled(),
                        Forms\Components\Textarea::make('confirmed_notes')
                            ->label('Catatan Konfirmasi')
                            ->disabled()
                            ->rows(3),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Tambahkan catatan jika diperlukan...')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpan('full'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('No.')
                    ->sortable()
                    ->alignCenter()
                    ->width('50px'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => $state ?? '-')
                    ->tooltip(fn ($record) => $record->user?->email),
                Tables\Columns\TextColumn::make('inventoriBuku.title')
                    ->label('Judul Buku')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('borrow_date')
                    ->label('Tgl Pinjam')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'borrowed' => 'info',
                        'returned' => 'success',
                        'overdue' => 'danger',
                        'lost' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'borrowed' => 'Dipinjam',
                        'returned' => 'Dikembalikan',
                        'overdue' => 'Terlambat',
                        'lost' => 'Hilang',
                    }),
                Tables\Columns\TextColumn::make('confirmation_status')
                    ->label('Konfirmasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'borrowed' => 'Dipinjam',
                        'returned' => 'Dikembalikan',
                        'overdue' => 'Terlambat',
                        'lost' => 'Hilang',
                    ]),
                Tables\Filters\SelectFilter::make('confirmation_status')
                    ->label('Konfirmasi')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('inventoriBuku')
                    ->label('Buku')
                    ->relationship('inventoriBuku', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->label('Setujui')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn ($record) => $record->confirmation_status === 'pending')
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Peminjaman')
                        ->modalDescription('Apakah Anda yakin ingin menyetujui peminjaman ini? Stok buku akan dikurangi.')
                        ->form([
                            Forms\Components\Textarea::make('confirmed_notes')
                                ->label('Catatan')
                                ->placeholder('Tambahkan catatan jika diperlukan...')
                                ->rows(3),
                        ])
                        ->action(function ($record, $data) {
                            $record->update([
                                'confirmation_status' => 'approved',
                                'confirmed_by' => Auth::id(),
                                'confirmed_at' => now(),
                                'confirmed_notes' => $data['confirmed_notes'] ?? null,
                            ]);
                            
                            // Kurangi stok buku
                            $book = $record->inventoriBuku;
                            if ($book && $book->quantity > 0) {
                                $book->decrement('quantity');
                            }
                        }),
                    Tables\Actions\Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->visible(fn ($record) => $record->confirmation_status === 'pending')
                        ->requiresConfirmation()
                        ->modalHeading('Tolak Peminjaman')
                        ->modalDescription('Apakah Anda yakin ingin menolak peminjaman ini?')
                        ->form([
                            Forms\Components\Textarea::make('confirmed_notes')
                                ->label('Alasan Penolakan')
                                ->required()
                                ->placeholder('Jelaskan alasan penolakan...')
                                ->rows(3),
                        ])
                        ->action(function ($record, $data) {
                            $record->update([
                                'confirmation_status' => 'rejected',
                                'confirmed_by' => Auth::id(),
                                'confirmed_at' => now(),
                                'confirmed_notes' => $data['confirmed_notes'],
                            ]);
                        }),
                ])
                ->label('Aksi Konfirmasi')
                ->icon('heroicon-o-cog')
                ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeminjamen::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            'edit' => Pages\EditPeminjaman::route('/{record}/edit'),
        ];
    }
}

