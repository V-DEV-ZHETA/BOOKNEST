<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengembalianResource\Pages;
use App\Filament\Resources\PengembalianResource\RelationManagers;
use App\Models\Pengembalian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PengembalianResource extends Resource
{
    protected static ?string $model = Pengembalian::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
    
    protected static ?string $navigationLabel = 'Pengembalian';

    protected static ?string $pluralModelLabel = 'Pengembalian';

    protected static ?string $modelLabel = 'Pengembalian Buku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Peminjaman')
                    ->description('Pilih peminjaman yang akan dicatat pengembaliannya')
                    ->schema([
                        Forms\Components\Select::make('peminjaman_id')
                            ->relationship('peminjaman', 'id')
                            ->required()
                            ->label('Peminjaman')
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(function (Model $record) {
                                return "#{$record->id} - " . ($record->user?->name ?? 'N/A') . " - " . ($record->inventoriBuku?->title ?? 'N/A');
                            })
                            ->helperText('Pilih peminjaman yang buku-nya akan dikembalikan')
                            ->searchPrompt('Cari berdasarkan nama peminjam atau judul buku...')
                            ->placeholder('Pilih peminjaman...')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $peminjaman = \App\Models\Peminjaman::with(['user', 'inventoriBuku'])->find($state);
                                    if ($peminjaman) {
                                        $set('user_id', $peminjaman->user_id);
                                        $set('inventori_buku_id', $peminjaman->inventori_buku_id);
                                        $set('return_date', now()->format('Y-m-d'));
                                    }
                                }
                            }),
                    ]),

                Forms\Components\Section::make('Detail Pengembalian')
                    ->description('Informasi detail tentang pengembalian buku')
                    ->schema([
                        Forms\Components\DatePicker::make('return_date')
                            ->required()
                            ->label('Tanggal Pengembalian')
                            ->default(now())
                            ->helperText('Tanggal buku dikembalikan')
                            ->prefixIcon('heroicon-o-calendar')
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Forms\Components\Select::make('condition')
                            ->options([
                                'good' => 'Baik',
                                'damaged' => 'Rusak Ringan',
                                'damaged_heavy' => 'Rusak Berat',
                                'lost' => 'Hilang',
                            ])
                            ->required()
                            ->label('Kondisi Buku')
                            ->default('good')
                            ->helperText('Kondisi buku saat dikembalikan')
                            ->prefixIcon('heroicon-o-book-open'),
                        Forms\Components\TextInput::make('late_days')
                            ->label('Keterlambatan (Hari)')
                            ->disabled()
                            ->helperText('Jumlah hari keterlambatan (jika ada)'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Denda & Catatan')
                    ->description('Informasi denda dan catatan tambahan')
                    ->schema([
                        Forms\Components\TextInput::make('fine_amount')
                            ->label('Jumlah Denda (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->helperText('Jumlah denda keterlambatan atau kerusakan')
                            ->prefixIcon('heroicon-o-currency-dollar'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Tambahkan catatan tentang kondisi buku atau pengembalian...')
                            ->maxLength(2000)
                            ->rows(4)
                            ->helperText('Catatan atau informasi tambahan tentang pengembalian')
                            ->columnSpan('full'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Status Konfirmasi')
                    ->description('Status verifikasi oleh staff')
                    ->schema([
                        Forms\Components\Select::make('confirmation_status')
                            ->options([
                                'pending' => 'Menunggu Konfirmasi',
                                'approved' => 'Dikonfirmasi',
                                'rejected' => 'Ditolak',
                            ])
                            ->required()
                            ->default('pending')
                            ->disabled(),
                    ])
                    ->columns(1),

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
                Tables\Columns\TextColumn::make('peminjaman.id')
                    ->label('ID Pinjam')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip('ID Peminjaman'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('peminjaman.inventoriBuku.title')
                    ->label('Judul Buku')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tgl Kembali')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('peminjaman.due_date')
                    ->label('Jatuh Tempo')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->return_date > $record->peminjaman->due_date ? 'danger' : null),
                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'good' => 'success',
                        'damaged' => 'warning',
                        'damaged_heavy' => 'danger',
                        'lost' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'good' => 'Baik',
                        'damaged' => 'Rusak Ringan',
                        'damaged_heavy' => 'Rusak Berat',
                        'lost' => 'Hilang',
                    }),
                Tables\Columns\TextColumn::make('fine_amount')
                    ->label('Denda')
                    ->money('IDR')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state > 0 ? number_format($state, 0, ',', '.') : '-'),
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
                        'approved' => 'Dikonfirmasi',
                        'rejected' => 'Ditolak',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'good' => 'Baik',
                        'damaged' => 'Rusak Ringan',
                        'damaged_heavy' => 'Rusak Berat',
                        'lost' => 'Hilang',
                    ]),
                Tables\Filters\SelectFilter::make('confirmation_status')
                    ->label('Konfirmasi')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Dikonfirmasi',
                        'rejected' => 'Ditolak',
                    ]),
                Tables\Filters\Filter::make('late')
                    ->label('Terlambat')
                    ->query(fn ($query) => $query->whereHas('peminjaman', function ($q) {
                        $q->whereColumn('return_date', '>', 'due_date');
                    }))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => $record->confirmation_status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengembalian')
                    ->modalDescription('Apakah Anda yakin ingin mengkonfirmasi pengembalian ini? Stok buku akan dikembalikan.')
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
                        
                        // Tambah kembali stok buku
                        $book = $record->inventoriBuku;
                        if ($book) {
                            $book->increment('quantity');
                        }
                        
                        // Update status peminjaman
                        $record->peminjaman->update([
                            'status' => 'returned',
                            'return_date' => $record->return_date,
                        ]);
                    }),
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
            'index' => Pages\ListPengembalians::route('/'),
            'create' => Pages\CreatePengembalian::route('/create'),
            'edit' => Pages\EditPengembalian::route('/{record}/edit'),
        ];
    }
}

