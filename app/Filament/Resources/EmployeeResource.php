<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{

    protected static ?string $navigationGroup = 'Tabel Pengguna';

    protected static ?string $modelLabel = 'Pegawai Toko';

    protected static ?string $navigationLabel = 'Pegawai Toko';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Data Pengguna')->description('Masukan data pengguna')->schema([

                    TextInput::make('name')->label('Nama Lenkap')->required()->maxLength(199),

                    TextInput::make('email')->label('Alamat Email')->required()->email()->maxLength(199),

                    Fieldset::make('Password')->schema([
                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->confirmed()->autocomplete(false)
                            ->required(fn ($record) => $record === null)
                            ->minLength(3)
                            ->maxLength(199),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Kata Sandi')
                            ->password()
                            ->autocomplete(false)
                            ->required(fn ($record) => $record === null)

                    ])->columns(1)->hiddenOn('view'),


                ])->columns(2),


                Section::make('Data Pegawai')->schema([

                    TextInput::make('employee.employee_code')->label('Kode Pegawai')->required()->length(6),

                    TextInput::make('employee.ktp_number')->label('Nomor KTP')->required()->length(16),

                    TextInput::make('employee.full_name')->label('Nama Lenkap')->required()->maxLength(199)->columnSpanFull(),

                    DatePicker::make('employee.start_working_at')->label('Mulai Bekerja')->required(),

                    TextInput::make('employee.store_code')->label('Kode Toko')->disabled(),

                ])->columns(2),


                Section::make('Data Pegawai')->hidden()->relationship('employee')->schema([

                    TextInput::make('employee_code'),

                    TextInput::make('ktp_number'),

                    TextInput::make('full_name'),

                    DatePicker::make('start_working_at'),

                    TextInput::make('store_code'),

                ])->columns(2),


                Section::make('Kontak Pengguna')->schema([
                    TextInput::make('phone_number.phone_code')->label('Kode Telepon')->required()->default('+62')->maxLength(5),
                    TextInput::make('phone_number.phone_number')->label('Nomor Telepon')->required()->maxLength(15)->tel(),
                ])->columns(2),

                Section::make('Kontak Pengguna')->hidden()->relationship('phone_number')->schema([
                    TextInput::make('phone_code')->label('Kode Telepon')->required()->default('+62')->maxLength(5)->disabled(),
                    TextInput::make('phone_number')->label('Nomor Telepon')->required()->maxLength(15)->tel()->disabled(),
                ])->columns(2),

            ]);
    }

    public static function canCreate(): bool
    {
        // Return false to disable the creation of new resources
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.ktp_number')->label('Nomor KTP'),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
}
