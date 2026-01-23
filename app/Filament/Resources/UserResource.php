<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna Sistem';

    protected static ?string $modelLabel = 'Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pribadi')
                    ->description('Data pribadi pengguna sistem')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Nama Lengkap')
                            ->placeholder('Masukkan nama lengkap')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-user')
                            ->autofocus(),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->label('Email')
                            ->placeholder('nama@email.com')
                            ->email()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-envelope')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone')
                            ->label('No. HP')
                            ->placeholder('081234567890')
                            ->tel()
                            ->maxLength(20)
                            ->prefixIcon('heroicon-o-phone'),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->maxDate(now())
                            ->prefixIcon('heroicon-o-calendar'),
                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'male' => 'Laki-laki',
                                'female' => 'Perempuan',
                            ])
                            ->prefixIcon('heroicon-o-users'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Keanggotaan')
                    ->description('Status dan periode keanggotaan')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                                'suspended' => 'D suspend',
                            ])
                            ->required()
                            ->label('Status')
                            ->default('active')
                            ->prefixIcon('heroicon-o-check-circle'),
                        Forms\Components\DatePicker::make('member_since')
                            ->label('Anggota Sejak')
                            ->default(now())
                            ->prefixIcon('heroicon-o-calendar-days'),
                        Forms\Components\DatePicker::make('member_until')
                            ->label('Anggota Hingga')
                            ->prefixIcon('heroicon-o-calendar'),
                        Forms\Components\TextInput::make('member_number')
                            ->label('Nomor Anggota')
                            ->placeholder('AUTO-GENERATE')
                            ->disabled()
                            ->maxLength(50)
                            ->prefixIcon('heroicon-o-identification'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Keamanan & Akses')
                    ->description('Pengaturan password dan peran pengguna')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->confirmed()
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-lock-closed')
                            ->helperText('Kosongkan jika tidak ingin mengubah password'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->prefixIcon('heroicon-o-lock-closed'),
                        Forms\Components\MultiSelect::make('roles')
                            ->label('Peran')
                            ->relationship('roles', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih satu atau lebih peran untuk pengguna')
                            ->columnSpan('full'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Alamat')
                    ->description('Informasi alamat pengguna')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->placeholder('Alamat lengkap...')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpan('full'),
                        Forms\Components\TextInput::make('city')
                            ->label('Kota')
                            ->placeholder('Jakarta')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Kode Pos')
                            ->placeholder('12345')
                            ->maxLength(10),
                    ])
                    ->columns(3),
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
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Foto')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'User') . '&background=random'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('No. HP')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-o-phone'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Peran')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => $state ?: '-'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'suspended' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'D suspend',
                    }),
                Tables\Columns\TextColumn::make('member_number')
                    ->label('No. Anggota')
                    ->searchable()
                    ->toggleable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('member_since')
                    ->label('Anggota Sejak')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Verifikasi Email')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'D suspend',
                    ]),
                Tables\Filters\Filter::make('verified')
                    ->label('Terverifikasi')
                    ->query(fn ($query) => $query->whereNotNull('email_verified_at'))
                    ->toggle(),
                Tables\Filters\Filter::make('unverified')
                    ->label('Belum Terverifikasi')
                    ->query(fn ($query) => $query->whereNull('email_verified_at'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('resetPassword')
                    ->label('Reset Password')
                    ->icon('heroicon-o-lock-closed')
                    ->color('warning')
                    ->action(function (User $record) {
                        $newPassword = 'password123'; // Default password
                        $record->update(['password' => Hash::make($newPassword)]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password')
                    ->modalDescription('Apakah Anda yakin ingin mereset password pengguna ini? Password akan diubah menjadi "password123".'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->action(fn ($records) => $records->each->update(['status' => 'active']))
                        ->color('success')
                        ->icon('heroicon-o-check-circle'),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->action(fn ($records) => $records->each->update(['status' => 'inactive']))
                        ->color('gray')
                        ->icon('heroicon-o-x-circle'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

