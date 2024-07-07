<?php

namespace App\Filament\Resources\StoreDashboard;

use App\Filament\Resources\StoreDashboard\AdjustStockResource\Pages;
use App\Filament\Resources\StoreDashboard\AdjustStockResource\RelationManagers;
use App\Models\AdjustStock;
use App\Traits\Ownership;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdjustStockResource extends Resource
{
    use Ownership;

    protected static ?string $model = AdjustStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'Produk';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return cek_store_role();
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function getNavigationLabel(): string
    {
        return 'Adjust Stock';
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


                Select::make('product_id')->label('Pilih Produk')
                    ->options(get_product_list(get_have_stock: false))
                    ->searchable()
                    ->columnSpanFull()
                    ->required(),

                Select::make('type')->label('Tipe Adjust')
                    ->options(['plus' => 'Plus (Lebih)', 'minus' => 'Minus (Kurang)'])
                    ->required(),

                TextInput::make('qty')->label('QTY Adjust')
                    ->required()
                    ->maxLength(100)
                    ->numeric()
                    ->minValue(config('rules.stock.min_input'))
                    ->maxValue(config('rules.stock.max_input')),

                TextInput::make('message')->label('Alasan perubahan')->required()->maxLength(200)
                    ->columnSpanFull()
                    ->autocapitalize('words')
                    ->datalist([
                        'Barang kurang',
                        'Barang lebih',
                        'Barang Rusak',
                        'Barang Kadaluarsa',
                        'Barang Hilang',
                    ]),

            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('message')->label('Pesan'),

                TextColumn::make('total_amount')
                    ->label('Total Adjust')
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp. '),

                TextColumn::make('total_qty')->label('QTY'),

                TextColumn::make('type')->label('Jenis')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'plus' => 'success',
                        'minus' => 'danger',
                    }),

                TextColumn::make('admin_name')->label('Nama Admin'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListAdjustStocks::route('/'),
            'create' => Pages\CreateAdjustStock::route('/create'),
            'edit' => Pages\EditAdjustStock::route('/{record}/edit'),
        ];
    }
}
