<?php

namespace App\Filament\Resources\StoreDashboard;

use App\Models\TransactionSale;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\StoreDashboard\TransactionSaleResource\Pages;
use App\Models\Debtor;
use App\Models\Product;
use App\Traits\Ownership;
use Filament\Forms\Components\{
    DatePicker,
    DateTimePicker,
    Fieldset,
    FileUpload,
    Repeater,
    Section,
    Select,
    Textarea,
    TextInput,
    Toggle
};
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
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

                // TextInput::make('total_amount')->label('Total Transaksi')
                //     ->disabled()
                //     ->default(0)
                //     ->prefix('RP'),

                // TextInput::make('total_profit')->label('Total Keuntungan')
                //     ->disabled()
                //     ->default(0)
                //     ->prefix('RP'),

                Repeater::make('products')->label('Daftar Produk')
                    ->hiddenOn('view')
                    ->schema([
                        Select::make('product_id')
                            ->label('Pilih Produk')
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
                    ->dehydrateStateUsing(function (Get $get, Set $set) {})
                    ->itemLabel(function (array $state, Set $set, Get $get) {

                        $qty    =   intval($state['product_qty'] ?? 0);

                        $product    =   Product::find($state['product_id'] ?? 0);

                        $potongan_per_product   =   ubah_angka_rupiah_ke_int($state['product_discount'] ?? 0);

                        $potongan_per_qty       =   ubah_angka_rupiah_ke_int($state['product_discount_per_qty'] ?? 0) * $qty;

                        $product_name   =   ucwords($product?->name ?? 'Produk');

                        $gross_trx      =   (($product?->sale_price ?? 0) * $qty - $potongan_per_product) - $potongan_per_qty;

                        if ($get('is_deliver')) {
                            $onkir      =   $product->delivery_fee ?? 0;
                            $gross_trx  +=  $onkir;
                        } else {
                            $onkir      =   0;
                        }

                        $profit         =   ubah_angka_int_ke_rupiah($gross_trx - ($product?->product_cost ?? 0) * $qty);

                        $trx_ammount    =   ubah_angka_int_ke_rupiah($gross_trx);

                        return  "$product_name | Transaksi Rp. $trx_ammount | Profit Rp. $profit | Onkir Rp." . ubah_angka_int_ke_rupiah($onkir);
                    })
                    ->live(false, 20)
                    ->reactive()
                    ->columns(3),

                Toggle::make('is_debt')
                    ->label('Transaksi Pinjaman')
                    ->default(false)
                    ->live()
                    ->onColor('success'),


                Toggle::make('is_deliver')
                    ->label('Pakai Onkir')
                    ->default(false)
                    ->live()
                    ->onColor('success'),

                Fieldset::make('debtor_data')
                    ->label('Data peminjam')
                    ->visible(function (Get $get) {
                        return $get('is_debt');
                    })
                    ->schema([

                        TextInput::make('debtor_data.name')
                            ->label('Nama')
                            ->required()
                            ->minLength(3)
                            ->maxLength(199),

                        TextInput::make('debtor_data.phone')
                            ->label('Nomor telepeon')
                            ->numeric()
                            ->inputMode('integer')
                            ->required(),

                        TextInput::make('debtor_data.amount')
                            ->label('Jumlah Pinjaman')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->prefix('RP'),

                        DateTimePicker::make('debtor_data.due_date')
                            ->label('Tanggal jatuh tempo')
                            ->minDate(now()->addDay())
                            ->required(),

                        Textarea::make('debtor_data.note')
                            ->label('Catatan/Keteragan')
                            ->columnSpanFull()
                            ->minLength(3)
                            ->maxLength(400),

                    ]),

                Repeater::make('transactionSaleItems')
                    ->relationship('transactionSaleItems')
                    ->label('Daftar Produk')
                    ->visibleOn('view')
                    ->columnSpanFull()
                    ->schema([

                        Fieldset::make('')
                            ->schema([

                                TextInput::make('product_sku')->label('SKU Produk'),

                                TextInput::make('product_name')->label('Nama Produk'),

                                TextInput::make('sale_price')->label('Penjualan')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->inputMode('double')
                                    ->required()
                                    ->prefix('Rp'),

                                TextInput::make('sale_profit')->label('Keutungan')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->inputMode('double')
                                    ->required()
                                    ->prefix('Rp'),
                            ]),

                    ]),

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([])
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total_qty')
                    ->label('QTY Jual')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total_amount')
                    ->label('Tranasksi')
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp. ')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total_profit')
                    ->label('Keuntungan')
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp. ')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('admin_name')
                    ->label('Nama Admin')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('updated_at')
                    ->label('Tanggal Diubah')
                    ->date('D d-m-Y')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->date('D d-m-Y')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\Filter::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Hingga Tanggal'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
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
