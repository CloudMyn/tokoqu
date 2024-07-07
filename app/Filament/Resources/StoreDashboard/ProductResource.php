<?php

namespace App\Filament\Resources\StoreDashboard;

use App\Filament\Resources\StoreDashboard\ProductResource\Pages;
use App\Models\Product;
use App\Traits\Ownership;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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

                TextInput::make('stock')
                    ->label('Base Stock')
                    ->required()
                    ->numeric()
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
            'index' => Pages\ListProducts::route('/index'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
