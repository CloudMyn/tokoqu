<?php

namespace App\Filament\Resources\Store;

use App\Filament\Fields\MoneyField;
use App\Filament\Resources\Store\StoreResource\Pages;
use App\Models\Store;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Middleware\Authenticate;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-users';
    public static function getNavigationLabel(): string
    {
        return 'Daftar Toko';
    }

    public static function getNavigationGroup(): ?string
    {
        if (get_auth_user()->has_role('store_owner')) {
            return 'Data Toko';
        }

        return 'Tabel Pengguna';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                FileUpload::make('image')->label('Foto Toko')
                    ->image()
                    ->imageEditor()
                    ->directory(get_user_directory('stores_files'))
                    ->columnSpanFull(),

                TextInput::make('name')->label('Nama Toko')->required()->maxLength(100),

                TextInput::make('code')->label('Kode Toko')->required()->length(4)->unique('stores', 'code', function ($record) {
                    return $record;
                }),

                TextInput::make('assets')->label('Kas Toko/Modal Toko')
                    ->mask(RawJs::make('$money($input)'))
                    ->required()
                    ->columnSpanFull()
                    ->numeric()
                    ->inputMode('double')
                    ->prefix('RP'),

                Textarea::make('address')->label('Alamat')->required()->maxLength(100)->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Kode Toko'),
                ImageColumn::make('image')->label('Foto')->default(function ($record) {
                    return $record?->image ?? null ? asset($record->image) : 'https://via.placeholder.com/150';
                }),
                TextColumn::make('name')->label('Nama'),
                TextColumn::make('address')->label('Alamat'),
                TextColumn::make('assets')
                    ->label('Kas Toko')
                    ->numeric()
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp. ')
                    ->default(function ($record) {
                        return "number_format($record->assets, 0, ',', '.')";
                    }),
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
            ]);
    }

    public static function canCreate(): bool
    {
        return cek_admin_role();
    }

    public static function canAccess(): bool
    {
        return cek_admin_role();
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
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}
