<?php

namespace App\Filament\Resources\StoreDashboard;

use App\Filament\Resources\StoreDashboard\TransactionBuyResource\Pages;
use App\Filament\Resources\StoreDashboard\TransactionBuyResource\RelationManagers;
use App\Models\TransactionBuy;
use App\Models\TransactionBuyItem;
use App\Traits\Ownership;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionBuyResource extends Resource
{
    use Ownership;

    protected static ?string $model = TransactionBuy::class;

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
        return 'Catat Pembelian';
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
                FileUpload::make('image')->label('Bukti Transaksi Pembelian/Nota pembelian')
                    ->image()
                    ->imageEditor()
                    ->directory(get_user_directory('/trx-buy'))
                    ->columnSpanFull(),

                TextInput::make('title')->label('Nama Transaksi')->required()->maxLength(100)->default(function () {
                    return ucwords("Transaksi Pembelian oleh " . get_auth_user()->name);
                })->autocapitalize('words'),

                Select::make('supplier_id')
                    ->label('Penyuplai Barang')
                    ->required()
                    ->options(get_suppliers()),

                TextInput::make('total_cost')->label('Jumlah transaksi')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->inputMode('double')
                    ->hiddenOn('view')
                    ->columnSpanFull()
                    ->required()
                    ->prefix('RP'),

                Repeater::make('products')->label('Daftar Produk')
                    ->schema([
                        Select::make('product_id')->label('Pilih Produk')
                            ->options(get_product_list())
                            ->required(),

                        \LaraZeus\Quantity\Components\Quantity::make('product_qty')
                            ->label('QTY Beli')
                            ->required()
                            ->minValue(config('rules.stock.min_input'))
                            ->maxValue(config('rules.stock.max_input')),

                        TextInput::make('product_cost')->label('Harga Barang')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->inputMode('double')
                            ->required()
                            ->prefix('RP'),
                    ])->columnSpanFull()->columns(3)->hiddenOn('view')->collapsible(),


                Section::make('Transaksi Pembelian')
                    ->schema([

                        TextInput::make('total_cost')->label('Jumlah transaksi')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->inputMode('double')
                            ->required()
                            ->prefix('RP'),


                        TextInput::make('total_qty')
                            ->label('QTY Beli')
                            ->minValue(config('rules.stock.min_input'))
                            ->maxValue(config('rules.stock.max_input'))
                            ->formatStateUsing(function ($state) {
                                return $state;
                            }),

                        TextInput::make('admin_name')->label('Nama Admin')->formatStateUsing(function ($state) {
                            return ucfirst($state);
                        })->columnSpanFull(),


                    ])->visibleOn('view')->columns(2),


                Repeater::make('transactionBuyItems')
                    ->relationship('transactionBuyItems')
                    ->label('Daftar Produk')
                    ->visibleOn('view')
                    ->columnSpanFull()
                    ->schema([

                        Fieldset::make('')
                            ->schema([

                                TextInput::make('product_sku')->label('SKU Produk'),

                                TextInput::make('product_name')->label('Nama Produk'),

                                TextInput::make('product_cost')->label('Harga Barang')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->inputMode('double')
                                    ->required()
                                    ->prefix('Rp'),

                                TextInput::make('total_qty')
                                    ->label('QTY Beli')
                                    ->minValue(config('rules.stock.min_input'))
                                    ->maxValue(config('rules.stock.max_input'))
                                    ->formatStateUsing(function ($state) {
                                        return $state;
                                    }),
                            ]),

                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([])
            ->columns([
                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_qty')
                    ->label('QTY Beli')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_cost')
                    ->label('Total Harga')
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp. ')
                    ->default(function ($record) {
                        return "number_format($record->total_cost, 0, ',', '.')";
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('admin_name')
                    ->label('Nama Admin')
                    ->searchable()
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
                Tables\Actions\ViewAction::make()->label('Tampilkan'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactionBuys::route('/'),
            'create' => Pages\CreateTransactionBuy::route('/create'),
            'edit' => Pages\EditTransactionBuy::route('/{record}/edit'),
        ];
    }
}
