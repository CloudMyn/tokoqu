<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Store;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Symfony\Component\Console\Input\Input;

use function Laravel\Prompts\form;

class EmployeeResource extends Resource
{

    protected static ?string $modelLabel = 'Pegawai Toko';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Pegawai Toko';
    }

    public static function getNavigationGroup(): ?string
    {
        if (get_auth_user()->has_role('store_owner')) {
            return 'Data Toko';
        }

        return 'Tabel Pengguna';
    }

    public static function getEloquentQuery(): Builder
    {
        $auth_user = get_auth_user();

        $base_query = parent::getEloquentQuery()->where('role', 'store_employee');

        if ($auth_user->has_role('store_owner')) {
            return User::whereHas('employee', function ($query) use ($auth_user) {
                return $query->where('store_code', get_context_store()?->code);
            });
        }

        return $base_query;
    }


    public static function form(Form $form): Form
    {
        $store_list = get_store_list();

        return $form
            ->schema([

                Section::make('Data Pengguna')->description('Masukan data pengguna')->schema([

                    TextInput::make('name')->label('Nama Lenkap')->required()->maxLength(199),

                    TextInput::make('email')->label('Alamat Email')->required()->email()->maxLength(199)->unique('users', 'email', function ($record) {
                        return $record;
                    }),

                    Fieldset::make('Password')->schema([
                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->confirmed()
                            ->autocomplete(false)
                            ->required(fn ($record) => $record === null)
                            ->minLength(3)
                            ->revealable()
                            ->maxLength(199),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Kata Sandi')
                            ->password()
                            ->revealable()
                            ->autocomplete(false),

                    ])->columns(1)->hiddenOn('view'),


                ])->columns(2),


                Section::make('Data Pegawai')->schema([

                    FileUpload::make('ktp_photo')->label('Foto KTP')
                        ->image()
                        ->imageEditor()
                        ->required(fn ($record) => $record === null)
                        ->directory(get_user_directory('stores_files'))
                        ->columnSpanFull(),

                    TextInput::make('employee_code')->label('Kode Pegawai')
                        ->required()
                        ->length(6)
                        ->unique('employees', 'employee_code', function ($record) {
                            return $record?->employee;
                        }),

                    TextInput::make('ktp_number')->label('Nomor KTP')->required()->length(16)->unique('employees', 'ktp_number', function ($record) {
                        return $record?->employee;
                    }),

                    TextInput::make('full_name')->label('Nama Lenkap')->required()->maxLength(199)->columnSpanFull(),

                    DatePicker::make('start_working_at')->label('Mulai Bekerja')->required(),

                    Select::make('store_code')->label('Pilih Toko')
                        ->options($store_list)
                        ->required(),


                ])->columns(2),


                Section::make('Kontak Pengguna')->schema([
                    TextInput::make('phone_code')->label('Kode Telepon')->required()->default('+62')->maxLength(5),
                    TextInput::make('phone_number')->label('Nomor Telepon')->required()->maxLength(15)->tel()->unique('phone_numbers', 'phone_number', function ($record) {
                        return $record?->phone_number;
                    }),
                ])->columns(2),


            ]);
    }

    public static function canCreate(): bool
    {
        return get_auth_user()->has_role(['store_owner']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.ktp_number')->label('Nomor KTP'),
                ImageColumn::make('employee.ktp_photo')->label('Foto KTP'),
                Tables\Columns\TextColumn::make('name')->label('Nama'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('employee.employee_code')->label('Kode Pegawai'),
                Tables\Columns\TextColumn::make('phone_number.phone_number')->label('Nomor Telepon'),
            ])
            ->filters([])
            ->modifyQueryUsing(function ($query) {
                return $query->where('role', 'store_employee');
            })
            ->actions([
                Tables\Actions\ViewAction::make()->label('Tampilkan'),
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return cek_store_role();
    }
}
