<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Hệ thống & Phân quyền';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'tài khoản';

    protected static ?string $pluralModelLabel = 'Quản lý tài khoản';

    protected static ?string $navigationLabel = 'Tài khoản & Phân quyền';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin tài khoản')
                    ->schema([
                        TextInput::make('name')
                            ->label('Họ và tên')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email đăng nhập')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Mật khẩu')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(12)
                            ->maxLength(255),
                        Toggle::make('is_admin')
                            ->label('Cho phép truy cập trang quản trị')
                            ->default(true),
                    ])
                    ->columns(2),
                Section::make('Vai trò & Quyền hạn (Spatie Permission)')
                    ->schema([
                        CheckboxList::make('roles')
                            ->label('Vai trò')
                            ->relationship('roles', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record): string => match ($record->name) {
                                'super_admin' => 'super_admin (Quản trị viên toàn quyền)',
                                'reviewer' => 'reviewer (Cán bộ thẩm định hồ sơ)',
                                'viewer' => 'viewer (Cán bộ tra cứu)',
                                default => $record->name,
                            })
                            ->columns(1),
                        CheckboxList::make('permissions')
                            ->label('Quyền bổ sung trực tiếp')
                            ->relationship('permissions', 'name')
                            ->columns(2),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Họ và tên')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('roles.name')
                    ->label('Vai trò')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'reviewer' => 'warning',
                        'viewer' => 'info',
                        default => 'gray',
                    }),
                IconColumn::make('is_admin')
                    ->label('Quản trị')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
