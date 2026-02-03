<?php

namespace App\Filament\Resources\UserPengembalianResource;

use App\Filament\Resources\UserPengembalianResource\Pages;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserPengembalianResource extends Resource
{
    protected static ?string $model = Pengembalian::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
    
    protected static ?string $navigationLabel = 'Pengembalian Saya';

    protected static ?string $pluralModelLabel = 'Pengembalian Saya';

    protected static ?string $modelLabel = 'Pengembalian';

    protected static bool $shouldRegisterNavigation = true;

    public static function getLabel(): string
    {
        return 'Pengembalian Saya';
    }

    public static function getPluralLabel(): string
    {
        return 'Pengembalian Saya';
    }

    public static function canAccess(): bool
    {
        return parent::canAccess() && 
               (request()->has('peminjaman_id') || 
                Pengembalian::where('user_id', Auth::id())->exists());
    }

    public static function form(Form $form): Form
    {
        $peminjamanId = request()->get('peminjaman_id');
        $peminjaman = null;
        
        if ($peminjamanId) {
            $peminjaman = Peminjaman::with(['user', 'inventoriBuku'])->find($peminjamanId);
        }

        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Peminjaman')
                    ->description('Pinjaman yang akan dikembalikan')
                    ->schema([
                        Forms\Components\TextInput::make('peminjaman_info')
                            ->label('Peminjaman')
                            ->disabled()
                            ->default($peminjaman 
                                ? "#{$peminjaman->id} - {$peminjaman->inventoriBuku?->title} - {$peminjaman->user?->name}"
                                : '')
                            ->helperText('Peminjaman yang buku-nya akan dikembalikan'),
                        Forms\Components\Hidden::make('peminjaman_id')
                            ->default($peminjamanId),
                        Forms\Components\Hidden::make('user_id')
                            ->default(Auth::id()),
                        Forms\Components\Hidden::make('inventori_buku_id')
                            ->default($peminjaman?->inventori_buku_id),
                    ]),

                Forms\Components\Section::make('Detail Pengembalian')
                    ->description('Informasi tentang pengembalian buku')
                    ->schema([
                        Forms\Components\DatePicker::make('return_date')
                            ->required()
                            ->label('Tanggal Pengembalian')
                            ->default(now())
                            ->disabled()
                            ->helperText('Tanggal Anda mengajukan pengembalian')
                            ->prefixIcon('heroicon-o-calendar'),
                        Forms\Components\Select::make('condition')
                            ->options([
                                'good' => 'Baik - Buku dalam kondisi baik tanpa kerusakan',
                                'damaged' => 'Rusak Ringan - Ada kerusakan minor (halaman robek, sampul kusut, dll)',
                                'damaged_heavy' => 'Rusak Berat - Buku rusak berat (halaman hilang, sampul sobek, dll)',
                                'lost' => 'Hilang - Buku tidak dapat ditemukan/ditemukan',
                            ])
                            ->required()
                            ->label('Kondisi Buku')
                            ->default('good')
                            ->helperText('Kondisi buku saat dikembalikan')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $fineAmount = 0;
                                
                                // Hitung denda berdasarkan kondisi
                                switch ($state) {
                                    case 'damaged':
                                        $fineAmount = 25000;
                                        break;
                                    case 'damaged_heavy':
                                        $fineAmount = 50000;
                                        break;
                                    case 'lost':
                                        $fineAmount = 100000;
                                        break;
                                }
                                
                                $set('fine_amount', $fineAmount);
                            }),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Jelaskan kondisi buku atau berikan informasi tambahan...')
                            ->maxLength(2000)
                            ->rows(4)
                            ->helperText('Catatan tentang kondisi buku atau alasan keterlambatan')
                            ->columnSpan('full'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Denda')
                    ->description('Perkiraan denda keterlambatan atau kerusakan')
                    ->schema([
                        Forms\Components\TextInput::make('fine_amount')
                            ->label('Perkiraan Denda (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->disabled()
                            ->helperText('Denda akan dikonfirmasi oleh staff perpustakaan'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Pengembalian::where('user_id', Auth::id())
                    ->with(['peminjaman.inventoriBuku', 'peminjaman.user'])
                    ->latest();
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('No.')
                    ->sortable()
                    ->alignCenter()
                    ->width('50px'),
                Tables\Columns\TextColumn::make('peminjaman.inventoriBuku.title')
                    ->label('Judul Buku')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('peminjaman.inventoriBuku.author')
                    ->label('Pengarang')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tgl Kembali')
                    ->date('d/m/Y')
                    ->sortable(),
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
                    ->label('Verifikasi')
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
                    ->label('Verifikasi')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Dikonfirmasi',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail'),
            ])
            ->emptyStateHeading('Belum ada pengembalian')
            ->emptyStateDescription('Riwayat pengembalian buku Anda akan muncul di sini.')
            ->emptyStateIcon('heroicon-o-arrow-uturn-left')
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
            'index' => Pages\ListUserPengembalians::route('/'),
            'create' => Pages\CreateUserPengembalian::route('/create'),
            'view' => Pages\ViewUserPengembalian::route('/{record}'),
        ];
    }
}

