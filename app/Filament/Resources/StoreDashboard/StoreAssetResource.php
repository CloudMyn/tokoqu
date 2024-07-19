<?php

namespace App\Filament\Resources\StoreDashboard;

use App\Filament\Resources\Store\StoreResource\StoreAssetResource\Pages\ListStoreAssets;
use App\Filament\Resources\StoreDashboard\StoreAssetResource\Pages\CreateStoreAsset;
use App\Filament\Resources\StoreDashboard\StoreAssetResource\Pages\EditStoreAsset;
use App\Models\StoreAsset;
use App\Traits\Ownership;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StoreAssetResource extends Resource
{
    use Ownership;

    protected static ?string $model = StoreAsset::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Kas Toko';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Transaksi';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => ListStoreAssets::route('/'),
            'create' => CreateStoreAsset::route('/create'),
            'edit' => EditStoreAsset::route('/{record}/edit'),
        ];
    }
}
