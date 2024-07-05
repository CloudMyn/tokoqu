<?php

namespace App\Filament\Resources\StoreDashboard;

use App\Models\TransactionSale;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\StoreDashboard\TransactionSaleResource\Pages;
use Filament\Forms\Components\{
    FileUpload,
    Repeater,
    Select,
    TextInput
};
use Filament\Forms\Form;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;

class TransactionSaleResource extends Resource
{
    protected static ?string $model = TransactionSale::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'Produk';

    public static function canAccess(): bool
    {
        return cek_store_role();
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getNavigationLabel(): string
    {
        return 'Jual Barang';
    }

    public static function getNavigationGroup(): ?string
    {
        if (cek_store_role()) {
            return 'Transaksi';
        }

        abort(403, 'Unauthorized');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')->label('Bukti Transaksi Penjualan/Nota Penjualan')
                    ->image()
                    ->imageEditor()
                    ->directory(get_user_directory('/trx-buy'))
                    ->columnSpanFull(),

                TextInput::make('title')->label('Nama Transaksi')->required()->maxLength(100)->default(function () {
                    return ucwords("Transaksi Penjualan Oleh " . get_auth_user()->name);
                })->autocapitalize('words'),

                TextInput::make('discount')->label('Potongan Diskon')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->inputMode('double')
                    ->prefix('RP'),

                Repeater::make('products')->label('Daftar Produk')
                    ->schema([
                        Select::make('product_id')->label('Pilih Produk')
                            ->options(get_product_list(get_have_stock: true))
                            ->searchable()
                            ->columnSpanFull()
                            ->required(),

                        // TextInput::make('sale_price')->label('Harga Jual')
                        //     ->mask(RawJs::make('$money($input)'))
                        //     ->readOnly(true)
                        //     ->prefix('RP'),

                        TextInput::make('product_qty')
                            ->label('QTY Beli')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(99999999),

                        TextInput::make('product_discount')->label('Potongan Produk Per-QTY')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->inputMode('double')
                            ->prefix('RP'),
                    ])
                    ->columnSpanFull()
                    ->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([])
            ->columns([
                TextColumn::make('title')->label('Title'),

                TextColumn::make('total_qty')->label('QTY Beli'),

                TextColumn::make('total_amount')
                    ->label('Tranasksi')
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp. '),

                TextColumn::make('total_profit')
                    ->label('Keuntungan')
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp. '),

                TextColumn::make('admin_name')->label('Nama Admin'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListTransactionSales::route('/'),
            'create' => Pages\CreateTransactionSale::route('/create'),
            'edit' => Pages\EditTransactionSale::route('/{record}/edit'),
        ];
    }
}
