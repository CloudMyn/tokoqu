<?php

namespace App\Filament\Resources\StoreDashboard;

use App\Models\TransactionSale;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\StoreDashboard\TransactionSaleResource\Pages;
use App\Models\Product;
use App\Traits\Ownership;
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
use Illuminate\Database\Eloquent\Builder;

class TransactionSaleResource extends Resource
{

    protected static ?string $model = TransactionSale::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'Produk';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return cek_store_role() || cek_store_employee_role() && cek_store_exists();
    }


    public static function getEloquentQuery(): Builder
    {
        parent::getEloquentQuery();

        if (cek_admin_role()) return parent::getEloquentQuery();

        $store   =   get_context_store();

        if (cek_store_employee_role()) {
            return parent::getEloquentQuery()->where('store_code', $store?->code)->orderBy('created_at', 'DESC')->where('admin_id', get_auth_user()->id);
        }

        return parent::getEloquentQuery()->where('store_code', $store?->code)->orderBy('created_at', 'DESC');
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getNavigationLabel(): string
    {
        return 'Catat Penjualan';
    }

    public static function getNavigationGroup(): ?string
    {
        if (cek_store_role()) {
            return 'Transaksi';
        }

        if (cek_store_employee_role()) return "";

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

                        \LaraZeus\Quantity\Components\Quantity::make('product_qty')
                            ->label('QTY Jual')
                            ->default(1)
                            ->required()
                            ->minValue(config('rules.stock.min_input'))
                            ->maxValue(config('rules.stock.max_input')),

                        TextInput::make('product_discount')->label('Potongan Per-Produk')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->inputMode('double')
                            ->prefix('RP'),

                        TextInput::make('product_discount_per_qty')->label('Potongan Per-QTY')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->inputMode('double')
                            ->prefix('RP'),
                    ])
                    ->collapsible()
                    ->cloneable()
                    ->columnSpanFull()
                    ->itemLabel(function (array $state) {

                        $qty    =   intval($state['product_qty'] ?? 0);

                        $product    =   Product::find($state['product_id'] ?? 0);

                        $potongan_per_product   =   ubah_angka_rupiah_ke_int($state['product_discount'] ?? 0);

                        $potongan_per_qty       =   ubah_angka_rupiah_ke_int($state['product_discount_per_qty'] ?? 0) * $qty;

                        $product_name   =   ucwords($product?->name ?? 'Produk');

                        $gross_trx      =   (($product?->sale_price ?? 0) * $qty - $potongan_per_product) - $potongan_per_qty;

                        $profit         =   ubah_angka_int_ke_rupiah($gross_trx - ($product?->product_cost ?? 0) * $qty);

                        $trx_ammount    =   ubah_angka_int_ke_rupiah($gross_trx);

                        return  "$product_name | Transaksi $trx_ammount | Profit $profit";
                    })
                    ->live(false, 20)
                    ->columns(3),

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([])
            ->columns([
                TextColumn::make('title')->label('Title'),

                TextColumn::make('total_qty')->label('QTY Jual'),

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
