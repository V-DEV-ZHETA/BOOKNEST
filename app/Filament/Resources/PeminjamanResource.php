<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Filament\Resources\PeminjamanResource\RelationManagers;
use App\Models\Peminjaman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

    protected static ?string $navigationIcon = 'grommet-transaction';
    
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
                            ->placeholder('Pilih buku...'),
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
                            ->prefixIcon('heroicon-o-calendar'),
                        Forms\Components\DatePicker::make('due_date')
                            ->required()
                            ->label('Tanggal Jatuh Tempo')
                            ->helperText('Tanggal maksimal buku harus dikembalikan')
                            ->prefixIcon('heroicon-o-clock'),
                        Forms\Components\DatePicker::make('return_date')
                            ->label('Tanggal Pengembalian')
                            ->helperText('Diisi saat buku dikembalikan (kosong jika belum dikembalikan)')
                            ->prefixIcon('heroicon-o-check-circle'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Status & Catatan')
                    ->description('Status peminjaman dan catatan tambahan')
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
                            ->helperText('Status peminjaman buku')
                            ->prefixIcon('heroicon-o-flag'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Tambahkan catatan jika diperlukan...')
                            ->maxLength(1000)
                            ->rows(3)
                            ->helperText('Catatan atau informasi tambahan tentang peminjaman')
                            ->columnSpan('full'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('borrower_name')
                                    ->label('Nama Peminjam (Jika bukan anggota)')
                                    ->placeholder('Nama lengkap peminjam')
                                    ->maxLength(255)
                                    ->helperText('Diisi jika peminjam bukan anggota perpustakaan'),
                                Forms\Components\TextInput::make('borrower_phone')
                                    ->label('No. HP Peminjam')
                                    ->placeholder('081234567890')
                                    ->tel()
                                    ->maxLength(20)
                                    ->helperText('Nomor HP yang dapat dihubungi'),
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => $state ?? $record->borrower_name)
                    ->tooltip(fn ($record) => $record->user?->email ?? $record->borrower_phone),
                Tables\Columns\TextColumn::make('inventoriBuku.title')
                    ->label('Judul Buku')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->formatStateUsing(fn ($state, $record) => $state ?? 'N/A'),
                Tables\Columns\TextColumn::make('inventoriBuku.isbn')
                    ->label('ISBN')
                    ->searchable()
                    ->fontFamily('mono')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('borrow_date')
                    ->label('Tgl Pinjam')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tgl Kembali')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
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
                Tables\Filters\SelectFilter::make('inventoriBuku')
                    ->label('Buku')
                    ->relationship('inventoriBuku', 'title')
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListPeminjamen::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            'edit' => Pages\EditPeminjaman::route('/{record}/edit'),
        ];
    }
}

