<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OwnerStoreResource\Pages;
use App\Filament\Resources\OwnerStoreResource\RelationManagers\PhoneNumberRelationManager;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OwnerStoreResource extends Resource
{
    protected static ?string $navigationGroup = 'Tabel Pengguna';

    protected static ?string $modelLabel = 'Pemilik Toko';

    protected static ?string $navigationLabel = 'Pemilik Toko';

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Data Pengguna')->description('Masukan data pengguna')->schema([

                    TextInput::make('name')->label('Nama Lenkap')->required()->maxLength(199),

                    TextInput::make('email')->label('Alamat Email')->required()->email()->maxLength(199),

                    Fieldset::make('Password')->schema([
                        TextInput::make('password')->label('Kata Sandi')->password()->confirmed()->autocomplete(false)->required()->minLength(3)->maxLength(199),
                        TextInput::make('password_confirmation')->label('Konfirmasi Kata Sandi')->password()->autocomplete(false)->required()
                    ])->columns(1)->hiddenOn('view'),
                ])->columns(2),

                Section::make('Data pemilik')->relationship('owner_store')->schema([

                    TextInput::make('name')->label('Nama Pemilik')->required()->maxLength(199)->columnSpanFull(),

                    TextInput::make('code')->label('Kode Owner')
                        ->length(6)
                        ->regex('/^[a-zA-Z0-9]+$/')
                        ->validationMessages([
                            'regex' => 'Input :attribute tidak valid!',
                        ]),

                    Select::make('level')->label('Level Pengguna')
                        ->options(['free' => 'Gratis', 'premium' => 'Premium', 'vip' => 'VIP'])
                        ->in(['free', 'gratis', 'premium'])
                        ->required(),
                ])->hidden(),

                Section::make('Data pemilik')->schema([
                    TextInput::make('owner_store.name')->label('Nama Pemilik')->required()->maxLength(199)->columnSpanFull(),
                    TextInput::make('owner_store.code')->label('Kode Owner')->length(6)->regex('/^[a-zA-Z0-9]+$/')->validationMessages([
                        'regex' => 'Input :attribute tidak valid!',
                    ]),
                    Select::make('owner_store.level')->label('Level Pengguna')->options([
                        'free' => 'Gratis',
                        'premium' => 'Premium',
                        'vip' => 'VIP',
                    ])->in(['free', 'gratis', 'premium'])->required(),
                ]),

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('phone_number.phone_number')->label('Nomor Telepon'),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->where('role', 'store_owner');
            })
            ->filters([])
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOwnerStores::route('/'),
            'create' => Pages\CreateOwnerStore::route('/create'),
            'edit' => Pages\EditOwnerStore::route('/{record}/edit'),
        ];
    }

    protected function getTitle(): string
    {
        return 'Edit Custom Post Title'; // Customize your title here
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'phone_number.phone_number'];
    }
}
