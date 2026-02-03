<?php

namespace App\Filament\Resources\UserPeminjamanResource;

use App\Filament\Resources\UserPeminjamanResource\Pages;
use App\Models\InventoriBuku;
use App\Models\Peminjaman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserPeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    
    protected static ?string $navigationLabel = 'Peminjaman Saya';

    protected static ?string $pluralModelLabel = 'Peminjaman Saya';

    protected static ?string $modelLabel = 'Peminjaman';

    protected static bool $shouldRegisterNavigation = true;

    public static function getLabel(): string
    {
        return 'Peminjaman Saya';
    }

    public static function getPluralLabel(): string
    {
        return 'Peminjaman Saya';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pinjam Buku')
                    ->description('Ajukan peminjaman buku dari perpustakaan')
                    ->schema([
                        Forms\Components\Select::make('inventori_buku_id')
                            ->label('Pilih Buku')
                            ->options(function () {
                                return InventoriBuku::where('quantity', '>', 0)
                                    ->pluck('title', 'id')
                                    ->mapWithKeys(function ($title, $id) {
                                        $book = InventoriBuku::find($id);
                                        return [$id => "{$title} - {$book->author} (Stok: {$book->quantity})"];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih buku yang ingin Anda pinjam')
                            ->searchPrompt('Cari berdasarkan judul buku...')
                            ->placeholder('Pilih buku...'),
                        
                        Forms\Components\DatePicker::make('borrow_date')
                            ->label('Tanggal Pinjam')
                            ->default(now())
                            ->disabled()
                            ->helperText('Tanggal pengajuan peminjaman'),
                        
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Tanggal Jatuh Tempo')
                            ->required()
                            ->default(now()->addDays(7))
                            ->helperText('Tanggal maksimal pengembalian (maksimal 14 hari)')
                            ->minDate(now())
                            ->maxDate(now()->addDays(14)),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Tambahkan catatan jika diperlukan... (opsional)')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpan('full')
                            ->helperText('Catatan tambahan untuk staff perpustakaan'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Peminjaman::where('user_id', Auth::id())
                    ->with(['inventoriBuku'])
                    ->latest();
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('No.')
                    ->sortable()
                    ->alignCenter()
                    ->width('50px'),
                Tables\Columns\TextColumn::make('inventoriBuku.title')
                    ->label('Judul Buku')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('inventoriBuku.author')
                    ->label('Pengarang')
                    ->searchable()
                    ->toggleable(),
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
                    ->label('Verifikasi')
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
                    ->label('Verifikasi')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail'),
                Tables\Actions\Action::make('return')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->visible(fn ($record) => $record->confirmation_status === 'approved' && $record->status === 'borrowed')
                    ->url(fn ($record) => route('filament.user.resources.user-pengembalians.create', ['peminjaman_id' => $record->id])),
            ])
            ->emptyStateHeading('Belum ada peminjaman')
            ->emptyStateDescription('Ajukan peminjaman buku pertama Anda!')
            ->emptyStateIcon('heroicon-o-book-open')
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
            'index' => Pages\ListUserPeminjamen::route('/'),
            'create' => Pages\CreateUserPeminjaman::route('/create'),
            'view' => Pages\ViewUserPeminjaman::route('/{record}'),
        ];
    }
}

