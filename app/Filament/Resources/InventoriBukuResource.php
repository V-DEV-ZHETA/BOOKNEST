<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoriBukuResource\Pages;
use App\Filament\Resources\InventoriBukuResource\RelationManagers;
use App\Models\InventoriBuku;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoriBukuResource extends Resource
{
    protected static ?string $model = InventoriBuku::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    
    protected static ?string $navigationLabel = 'Inventori Buku';

    protected static ?string $pluralModelLabel = 'Inventori Buku';

    protected static ?string $modelLabel = 'Buku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Utama')
                    ->description('Masukkan informasi dasar buku')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->label('Judul Buku')
                            ->placeholder('Masukkan judul buku')
                            ->maxLength(255)
                            ->helperText('Judul lengkap buku sesuai sampul'),
                        Forms\Components\TextInput::make('author')
                            ->required()
                            ->label('Pengarang')
                            ->placeholder('Masukkan nama pengarang')
                            ->maxLength(255)
                            ->helperText('Nama penulis atau penulis buku'),
                        Forms\Components\TextInput::make('isbn')
                            ->required()
                            ->label('ISBN')
                            ->placeholder('Masukkan nomor ISBN')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('International Standard Book Number (13 digit)')
                            ->prefixIcon('heroicon-o-identification'),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah Stok')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(0)
                            ->helperText('Jumlah buku yang tersedia di perpustakaan')
                            ->prefixIcon('heroicon-o-numbered-list'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Deskripsi & Detail')
                    ->description('Masukkan deskripsi dan detail tambahan')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Buku')
                            ->placeholder('Masukkan sinopsis atau deskripsi buku...')
                            ->maxLength(65535)
                            ->rows(5)
                            ->helperText('Sinopsys, ringkasan, atau informasi tambahan tentang buku')
                            ->columnSpan('full'),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('publisher')
                                    ->label('Penerbit')
                                    ->placeholder('Nama penerbit')
                                    ->maxLength(255)
                                    ->helperText('Penerbit buku'),
                                Forms\Components\TextInput::make('year')
                                    ->label('Tahun Terbit')
                                    ->placeholder('2024')
                                    ->numeric()
                                    ->maxLength(4)
                                    ->helperText('Tahun buku diterbitkan'),
                                Forms\Components\TextInput::make('edition')
                                    ->label('Edisi')
                                    ->placeholder('Cetakan 1')
                                    ->maxLength(50)
                                    ->helperText('Edisi atau cetakan buku'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('language')
                                    ->label('Bahasa')
                                    ->placeholder('Indonesia')
                                    ->maxLength(50)
                                    ->helperText('Bahasa yang digunakan dalam buku'),
                                Forms\Components\TextInput::make('pages')
                                    ->label('Jumlah Halaman')
                                    ->placeholder('250')
                                    ->numeric()
                                    ->helperText('Total halaman buku'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('category')
                                    ->label('Kategori')
                                    ->placeholder('Fiksi, Non-Fiksi, dll')
                                    ->maxLength(100)
                                    ->helperText('Kategori atau genre buku'),
                                Forms\Components\TextInput::make('location')
                                    ->label('Lokasi Rak')
                                    ->placeholder('A-1, B-2, dll')
                                    ->maxLength(50)
                                    ->helperText('Lokasi penyimpanan di perpustakaan'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Buku')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('author')
                    ->label('Pengarang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('isbn')
                    ->label('ISBN')
                    ->searchable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Stok')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('publisher')
                    ->label('Penerbit')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Diubah')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListInventoriBukus::route('/'),
            'create' => Pages\CreateInventoriBuku::route('/create'),
            'edit' => Pages\EditInventoriBuku::route('/{record}/edit'),
        ];
    }
}

