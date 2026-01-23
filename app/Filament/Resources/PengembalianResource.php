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

class PengembalianResource extends Resource
{
    protected static ?string $model = Pengembalian::class;

    protected static ?string $navigationIcon = 'grommet-transaction';
    
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
                                return "#{$record->id} - " . ($record->user?->name ?? $record->borrower_name) . " - " . ($record->inventoriBuku?->title ?? 'N/A');
                            })
                            ->helperText('Pilih peminjaman yang buku-nya akan dikembalikan')
                            ->searchPrompt('Cari berdasarkan nama peminjam atau judul buku...')
                            ->placeholder('Pilih peminjaman...')
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $peminjaman = \App\Models\Peminjaman::with(['user', 'inventoriBuku'])->find($state);
                                    if ($peminjaman) {
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
                            ->prefixIcon('heroicon-o-calendar'),
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
                        Forms\Components\Select::make('late_days')
                            ->label('Keterlambatan (Hari)')
                            ->disabled()
                            ->helperText('Jumlah hari keterlambatan (jika ada)')
                            ->afterStateHydrated(function ($component, $state) {
                                // This will be calculated automatically based on due_date
                            }),
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
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('received_by')
                                    ->label('Diterima Oleh')
                                    ->placeholder('Nama petugas yang menerima')
                                    ->maxLength(255)
                                    ->helperText('Nama petugas perpustakaan'),
                                Forms\Components\TextInput::make('checked_by')
                                    ->label('Diperiksa Oleh')
                                    ->placeholder('Nama petugas yang memeriksa')
                                    ->maxLength(255)
                                    ->helperText('Nama petugas yang memeriksa kondisi buku'),
                            ]),
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
                Tables\Columns\TextColumn::make('peminjaman.id')
                    ->label('ID Pinjam')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip('ID Peminjaman'),
                Tables\Columns\TextColumn::make('peminjaman.user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => $state ?? $record->peminjaman?->borrower_name),
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
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state : '-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
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
                Tables\Filters\Filter::make('late')
                    ->label('Terlambat')
                    ->query(fn ($query) => $query->whereHas('peminjaman', function ($q) {
                        $q->whereColumn('return_date', '>', 'due_date');
                    }))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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

