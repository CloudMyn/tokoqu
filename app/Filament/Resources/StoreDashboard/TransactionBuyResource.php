<?php

namespace App\Filament\Resources\StoreDashboard;

use App\Filament\Resources\StoreDashboard\TransactionBuyResource\Pages;
use App\Filament\Resources\StoreDashboard\TransactionBuyResource\RelationManagers;
use App\Models\TransactionBuy;
use App\Models\TransactionBuyItem;
use App\Traits\Ownership;
use Filament\Forms;
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

                TextInput::make('supplier')->label('Penyuplai Barang')->required()->maxLength(100)->autocapitalize('words'),

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
                            ->default(1)
                            ->required()
                            ->minValue(config('rules.stock.min_input'))
                            ->maxValue(config('rules.stock.max_input'))
                            ->readOnly(function ($state) {
                                return $state;
                            }),

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


                        TextInput::make('product_qty')
                            ->label('QTY Beli')
                            ->minValue(config('rules.stock.min_input'))
                            ->maxValue(config('rules.stock.max_input'))
                            ->formatStateUsing(function ($state) {
                                return $state;
                            }),

                        TextInput::make('admin_name')->label('Nama Admin')->formatStateUsing(function ($state) {
                            return ucfirst($state);
                        })->columnSpanFull(),


                    ])->visibleOn('view')->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([])
            ->columns([
                TextColumn::make('supplier')->label('Supplier'),

                TextColumn::make('title')->label('Title'),

                TextColumn::make('total_qty')->label('QTY Beli'),

                TextColumn::make('total_cost')
                    ->label('Total Harga')
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp. ')
                    ->default(function ($record) {
                        return "number_format($record->total_cost, 0, ',', '.')";
                    }),

                TextColumn::make('admin_name')->label('Nama Admin'),
            ])
            ->filters([
                //
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
