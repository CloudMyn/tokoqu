<?php

namespace App\Filament\Resources\StoreDashboard;

use App\Filament\Exports\ProductExporter;
use App\Filament\Resources\StoreDashboard\ProductResource\Pages;
use App\Filament\Resources\StoreDashboard\ProductResource\Pages\ViewProduct;
use App\Filament\Resources\StoreDashboard\TransactionSaleResource\Widgets\TrxSaleChart;
use App\Models\Product;
use App\Traits\Ownership;
use Filament\Actions\SelectAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Support\Enums\FontWeight;

class ProductResource extends Resource
{
    use Ownership;

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return cek_store_role();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku'];
    }

    public static function getNavigationLabel(): string
    {
        return 'Daftar Produk';
    }

    public static function getNavigationGroup(): ?string
    {
        if (cek_store_role()) {
            return 'Inventori';
        }

        abort(403, 'Unauthorized');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                FileUpload::make('image')->label('Foto Produk')
                    ->image()
                    ->imageEditor()
                    ->directory(get_user_directory('/products'))
                    ->columnSpanFull(),

                TextInput::make('name')->label('Nama Produk')->required()->maxLength(100),

                TextInput::make('supplier')->label('Penyuplai Barang')->required()->maxLength(100),

                TextInput::make('sku')->label('SKU produk')->required()->length(6)->unique('products', 'sku', function ($record) {
                    return $record;
                }),

                \LaraZeus\Quantity\Components\Quantity::make('stock')
                    ->default(0)
                    ->label('Base Stock')
                    ->required()
                    ->minValue(config('rules.stock.min_input'))
                    ->maxValue(config('rules.stock.max_input'))
                    ->readOnly(function ($record) {
                        return $record;
                    }),

                TextInput::make('sale_price')->label('Harga Jual')
                    ->mask(RawJs::make('$money($input)'))
                    ->required()
                    ->inputMode('double')
                    ->prefix('RP'),

                TextInput::make('product_cost')->label('Harga Beli')
                    ->mask(RawJs::make('$money($input)'))
                    ->required()
                    ->inputMode('double')
                    ->prefix('RP'),

                Select::make('store_code')->label('Pilih Toko')
                    ->options(get_store_list())
                    ->required()->columnSpanFull(),

                TextInput::make('fraction')->label('Fraction')->required()->numeric()->minValue(1)->maxValue(99999999),

                Select::make('unit')->label('Satuan')->required()->options(get_unit_list()),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('sku')->label('SKU'),

                ImageColumn::make('image')->label('Foto')->default(function ($record) {
                    return $record?->image ?? null ? asset($record->image) : 'https://via.placeholder.com/150';
                }),

                TextColumn::make('name')->label('Nama'),

                TextColumn::make('stock')->label('Stok'),

                TextColumn::make('fraction')->label('Unit')->suffix(function ($record) {
                    return '/' . ucwords($record->unit);
                }),

                TextColumn::make('sale_price')
                    ->label('Harga Jual')
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp. ')
                    ->default(function ($record) {
                        return "number_format($record->sale_price, 0, ',', '.')";
                    }),

                TextColumn::make('product_cost')
                    ->label('Harga Beli')
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp. ')
                    ->default(function ($record) {
                        return "number_format($record->prouct_cost, 0, ',', '.')";
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ProductExporter::class)->label('Eksport Produk'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Tampilkan'),
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus Dipilih'),
                ])->label('Aksi'),
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
            'index' => Pages\ListProducts::route('/index'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view'  =>  ViewProduct::route('/view/{record}'),
        ];
    }


    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Tabs::make('tabs')
                    ->tabs([

                        Tab::make('PRODUK')->schema([

                            Split::make([

                                Section::make([
                                    Fieldset::make('Data Produk')->schema([
                                        TextEntry::make('name')
                                            ->label('NAMA PRODUK')
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntrySize::Large),

                                        TextEntry::make('sku')
                                            ->label('SKU PRODUK')
                                            ->prefix('#')
                                            ->copyable()
                                            ->size(TextEntrySize::Large)
                                            ->copyMessage('SKU produk disalin')
                                            ->weight(FontWeight::Bold),

                                        TextEntry::make('sale_price')
                                            ->label('HARGA JUAL')
                                            ->size(TextEntrySize::Large)
                                            ->numeric(decimalPlaces: 0)
                                            ->prefix('Rp. '),

                                        TextEntry::make('product_cost')
                                            ->label('HARGA BELI/MODAL')
                                            ->size(TextEntrySize::Large)
                                            ->numeric(decimalPlaces: 0)
                                            ->prefix('Rp. '),

                                    ])->columns(2),


                                    RepeatableEntry::make('transaction_sale_items')
                                        ->label('10 PENJUALAN TERAHKIR')
                                        ->schema([
                                            TextEntry::make('transaction_id')
                                                ->label('')
                                                ->prefix('TRANSAKSI ID #')
                                                ->suffix(function ($record) {
                                                    return " | " . $record->created_at->format('d-m-Y H:i:s');
                                                })
                                                ->size(TextEntrySize::Small)
                                                ->columnSpanFull()
                                                ->numeric(decimalPlaces: 0),

                                            TextEntry::make('total_qty')
                                                ->label('QTY Jual')
                                                ->size(TextEntrySize::Small)
                                                ->numeric(decimalPlaces: 0),

                                            TextEntry::make('sale_price')
                                                ->label('Harga Jual')
                                                ->size(TextEntrySize::Small)
                                                ->numeric(decimalPlaces: 0)
                                                ->prefix('Rp. '),

                                            TextEntry::make('sale_profit')
                                                ->label('Keuntungan')
                                                ->size(TextEntrySize::Small)
                                                ->numeric(decimalPlaces: 0)
                                                ->prefix('Rp. '),
                                        ])
                                        ->columns(3)

                                ])->grow(true),

                                Section::make([
                                    ImageEntry::make('image')->label('Gambar Produk')->square()->width(340)->alignCenter(),

                                    TextEntry::make('stock')
                                        ->inlineLabel(true)
                                        ->badge()
                                        ->color(function ($record) {
                                            return $record->stock >= 10 ? 'success' : 'danger';
                                        })
                                        ->label('STOK BARANG'),

                                    TextEntry::make('supplier')
                                        ->label('PENYUPLAI')
                                        ->badge()
                                        ->color('info')
                                        ->inlineLabel(),

                                    TextEntry::make('unit')
                                        ->label('SATUAN')
                                        ->badge()
                                        ->color('info')
                                        ->inlineLabel(),

                                    TextEntry::make('fraction')
                                        ->label('FRAKSI')
                                        ->badge()
                                        ->color('info')
                                        ->inlineLabel(),

                                    TextEntry::make('created_at')
                                        ->label('DIBUAT PADA')
                                        ->inlineLabel(true)
                                        ->color('info')
                                        ->badge()
                                        ->dateTime(),

                                ])->grow(false),



                            ])->from('md')->columns(2)
                        ]),

                        Tab::make('LAPORAN')
                            ->schema([

                                Split::make([

                                    Section::make([

                                        KeyValueEntry::make('product_reports')->label('LAPORAN PENJUALAN PRODUK BULAN INI')->valueLabel('NILAI')->keyLabel('JENIS LAPORAN'),

                                    ])->key('product_reports')
                                        ->headerActions([

                                            Action::make('Export laporan')
                                                ->icon('heroicon-c-clipboard-document-list')
                                                ->requiresConfirmation(),
                                        ])
                                        ->grow(true),

                                    Section::make([

                                        TextEntry::make('name')
                                            ->label('NAMA')
                                            ->weight(FontWeight::Bold)
                                            ->inlineLabel(true),

                                        TextEntry::make('sku')
                                            ->label('SKU')
                                            ->prefix('#')
                                            ->copyable()
                                            ->inlineLabel(true)
                                            ->copyMessage('SKU produk disalin')
                                            ->weight(FontWeight::Bold),

                                        TextEntry::make('sale_price')
                                            ->label('HARGA')
                                            ->inlineLabel(true)
                                            ->numeric(decimalPlaces: 0)
                                            ->prefix('Rp. '),

                                        TextEntry::make('product_cost')
                                            ->label('MODAL')
                                            ->inlineLabel(true)
                                            ->numeric(decimalPlaces: 0)
                                            ->prefix('Rp. '),

                                        TextEntry::make('stock')
                                            ->inlineLabel(true)
                                            ->badge()
                                            ->color(function ($record) {
                                                return $record->stock >= 10 ? 'success' : 'danger';
                                            })
                                            ->label('STOK BARANG'),

                                        TextEntry::make('supplier')
                                            ->label('PENYUPLAI')
                                            ->badge()
                                            ->color('info')
                                            ->inlineLabel(),

                                        TextEntry::make('unit')
                                            ->label('SATUAN')
                                            ->badge()
                                            ->color('info')
                                            ->inlineLabel(),

                                        TextEntry::make('fraction')
                                            ->label('FRAKSI')
                                            ->badge()
                                            ->color('info')
                                            ->inlineLabel(),

                                        TextEntry::make('created_at')
                                            ->label('DIBUAT PADA')
                                            ->inlineLabel(true)
                                            ->color('info')
                                            ->badge()
                                            ->dateTime(),

                                    ])->grow(false),



                                ])->from('md')->columns(2)
                            ]),


                    ])
                    ->contained(false)
                    ->persistTab()
                    ->id('product_tab'),

            ])->columns(1);
    }
}
