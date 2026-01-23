<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanResource\Pages;
use App\Filament\Resources\LaporanResource\RelationManagers;
use App\Models\Laporan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LaporanResource extends Resource
{
    protected static ?string $model = Laporan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $pluralModelLabel = 'Laporan';

    protected static ?string $modelLabel = 'Laporan Perpustakaan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Laporan')
                    ->description('Isi informasi dasar laporan')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->label('Judul Laporan')
                            ->placeholder('Masukkan judul laporan')
                            ->maxLength(255)
                            ->helperText('Judul atau nama laporan yang akan dibuat'),
                        Forms\Components\Select::make('type')
                            ->options([
                                'peminjaman' => 'Laporan Peminjaman',
                                'pengembalian' => 'Laporan Pengembalian',
                                'keterlambatan' => 'Laporan Keterlambatan',
                                'kerusakan' => 'Laporan Kerusakan Buku',
                                'kehilangan' => 'Laporan Kehilangan Buku',
                                'inventaris' => 'Laporan Inventaris',
                                'anggota' => 'Laporan Keanggotaan',
                                'denda' => 'Laporan Denda',
                                'statistik' => 'Laporan Statistik',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required()
                            ->label('Jenis Laporan')
                            ->helperText('Pilih kategori jenis laporan')
                            ->searchable(),
                        Forms\Components\DatePicker::make('generated_at')
                            ->label('Tanggal Laporan')
                            ->default(now())
                            ->helperText('Tanggal laporan dibuat atau berlaku')
                            ->prefixIcon('heroicon-o-calendar'),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Dibuat Oleh')
                            ->searchable()
                            ->preload()
                            ->helperText('Petugas yang membuat laporan'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Isi Laporan')
                    ->description('Masukkan konten atau isi laporan')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('Konten Laporan')
                            ->required()
                            ->maxLength(65535)
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'blockquote',
                                'link',
                            ])
                            ->helperText('Isi lengkap laporan dapat ditulis di sini')
                            ->columnSpan('full'),
                    ]),

                Forms\Components\Section::make('Periode Laporan')
                    ->description('Atur periode waktu untuk laporan')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->helperText('Periode awal laporan'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->helperText('Periode akhir laporan'),
                        Forms\Components\TextInput::make('period_type')
                            ->label('Tipe Periode')
                            ->placeholder('Harian/Mingguan/Bulanan/Tahunan')
                            ->maxLength(50)
                            ->helperText('Tipe periode laporan'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Ringkasan & Metadata')
                    ->description('Informasi ringkasan dan metadata tambahan')
                    ->schema([
                        Forms\Components\Textarea::make('summary')
                            ->label('Ringkasan')
                            ->placeholder('Ringkasan singkat laporan...')
                            ->maxLength(2000)
                            ->rows(3)
                            ->helperText('Ringkasan eksekutif dari laporan')
                            ->columnSpan('full'),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('total_records')
                                    ->label('Total Data')
                                    ->numeric()
                                    ->helperText('Jumlah total data dalam laporan'),
                                Forms\Components\TextInput::make('page_count')
                                    ->label('Jumlah Halaman')
                                    ->numeric()
                                    ->helperText('Jumlah halaman laporan'),
                                Forms\Components\TextInput::make('file_path')
                                    ->label('Lokasi File')
                                    ->placeholder('path/to/file.pdf')
                                    ->maxLength(255)
                                    ->helperText('Lokasi file laporan jika ada'),
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
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Laporan')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'peminjaman' => 'info',
                        'pengembalian' => 'success',
                        'keterlambatan' => 'warning',
                        'kerusakan' => 'danger',
                        'kehilangan' => 'danger',
                        'inventaris' => 'primary',
                        'anggota' => 'secondary',
                        'denda' => 'info',
                        'statistik' => 'primary',
                        'lainnya' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'peminjaman' => 'Peminjaman',
                        'pengembalian' => 'Pengembalian',
                        'keterlambatan' => 'Keterlambatan',
                        'kerusakan' => 'Kerusakan',
                        'kehilangan' => 'Kehilangan',
                        'inventaris' => 'Inventaris',
                        'anggota' => 'Keanggotaan',
                        'denda' => 'Denda',
                        'statistik' => 'Statistik',
                        'lainnya' => 'Lainnya',
                    }),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pembuat')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('generated_at')
                    ->label('Tgl Laporan')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('period_type')
                    ->label('Periode')
                    ->toggleable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('total_records')
                    ->label('Total Data')
                    ->numeric()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Input')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tgl Ubah')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis Laporan')
                    ->options([
                        'peminjaman' => 'Peminjaman',
                        'pengembalian' => 'Pengembalian',
                        'keterlambatan' => 'Keterlambatan',
                        'kerusakan' => 'Kerusakan',
                        'kehilangan' => 'Kehilangan',
                        'inventaris' => 'Inventaris',
                        'anggota' => 'Keanggotaan',
                        'denda' => 'Denda',
                        'statistik' => 'Statistik',
                        'lainnya' => 'Lainnya',
                    ]),
                Tables\Filters\Filter::make('date_range')
                    ->label('Periode')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('to'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('generated_at', '>=', $data['from']))
                            ->when($data['to'], fn ($q) => $q->whereDate('generated_at', '<=', $data['to']));
                    }),
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
            'index' => Pages\ListLaporans::route('/'),
            'create' => Pages\CreateLaporan::route('/create'),
            'edit' => Pages\EditLaporan::route('/{record}/edit'),
        ];
    }
}

