<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaturanResource\Pages;
use App\Filament\Resources\PengaturanResource\RelationManagers;
use App\Models\Pengaturan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PengaturanResource extends Resource
{
    protected static ?string $model = Pengaturan::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $pluralModelLabel = 'Pengaturan Sistem';

    protected static ?string $modelLabel = 'Pengaturan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengaturan')
                    ->description('Konfigurasi kunci dan nilai pengaturan sistem')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->required()
                            ->label('Kunci (Key)')
                            ->placeholder('nama_pengaturan')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Nama unik untuk pengaturan (huruf kecil, underscore)')
                            ->prefixIcon('heroicon-o-key'),
                        Forms\Components\Select::make('type')
                            ->options([
                                'string' => 'Teks (String)',
                                'integer' => 'Angka (Integer)',
                                'float' => 'Desimal (Float)',
                                'boolean' => 'Ya/Tidak (Boolean)',
                                'json' => 'JSON',
                                'array' => 'Array/Daftar',
                                'date' => 'Tanggal',
                                'datetime' => 'Tanggal & Waktu',
                            ])
                            ->required()
                            ->label('Tipe Data')
                            ->default('string')
                            ->helperText('Tipe data untuk nilai pengaturan')
                            ->prefixIcon('heroicon-o-tag'),
                        Forms\Components\TextInput::make('group')
                            ->label('Grup')
                            ->placeholder('umum, tampilan, sistem, dll')
                            ->maxLength(50)
                            ->helperText('Pengelompokan pengaturan (opsional)'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Nilai Pengaturan')
                    ->description('Masukkan nilai untuk pengaturan ini')
                    ->schema([
                        Forms\Components\Textarea::make('value')
                            ->required()
                            ->label('Nilai (Value)')
                            ->placeholder('Masukkan nilai pengaturan...')
                            ->rows(5)
                            ->maxLength(65535)
                            ->helperText('Nilai pengaturan. Untuk JSON/Array, gunakan format yang sesuai.')
                            ->columnSpan('full'),
                    ]),

                Forms\Components\Section::make('Deskripsi & Keterangan')
                    ->description('Informasi tambahan tentang pengaturan')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Jelaskan fungsi dan kegunaan pengaturan ini...')
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('Penjelasan singkat tentang pengaturan ini')
                            ->columnSpan('full'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('default_value')
                                    ->label('Nilai Default')
                                    ->placeholder('Nilai default pengaturan')
                                    ->maxLength(255)
                                    ->helperText('Nilai default jika belum disetting'),
                                Forms\Components\TextInput::make('validation_rule')
                                    ->label('Aturan Validasi')
                                    ->placeholder('required|max:255')
                                    ->maxLength(255)
                                    ->helperText('Aturan validasi Laravel'),
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
                Tables\Columns\TextColumn::make('key')
                    ->label('Kunci')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'string' => 'primary',
                        'integer' => 'success',
                        'float' => 'success',
                        'boolean' => 'warning',
                        'json' => 'info',
                        'array' => 'info',
                        'date' => 'secondary',
                        'datetime' => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'string' => 'Teks',
                        'integer' => 'Angka',
                        'float' => 'Desimal',
                        'boolean' => 'Ya/Tidak',
                        'json' => 'JSON',
                        'array' => 'Array',
                        'date' => 'Tanggal',
                        'datetime' => 'Tgl & Waktu',
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai')
                    ->limit(50)
                    ->wrap()
                    ->formatStateUsing(function ($state, $record) {
                        if (strlen($state) > 50) {
                            return substr($state, 0, 50) . '...';
                        }
                        return $state;
                    })
                    ->tooltip(fn ($record) => $record->value),
                Tables\Columns\TextColumn::make('group')
                    ->label('Grup')
                    ->badge()
                    ->color('secondary')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Dibuat')
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
                    ->label('Tipe Data')
                    ->options([
                        'string' => 'Teks',
                        'integer' => 'Angka',
                        'float' => 'Desimal',
                        'boolean' => 'Ya/Tidak',
                        'json' => 'JSON',
                        'array' => 'Array',
                        'date' => 'Tanggal',
                        'datetime' => 'Tgl & Waktu',
                    ]),
                Tables\Filters\Filter::make('group')
                    ->label('Grup')
                    ->form([
                        Forms\Components\TextInput::make('group_name')
                            ->label('Nama Grup'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['group_name'], fn ($q) => $q->where('group', 'like', '%' . $data['group_name'] . '%'));
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
            ->defaultSort('group');
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
            'index' => Pages\ListPengaturans::route('/'),
            'create' => Pages\CreatePengaturan::route('/create'),
            'edit' => Pages\EditPengaturan::route('/{record}/edit'),
        ];
    }
}

