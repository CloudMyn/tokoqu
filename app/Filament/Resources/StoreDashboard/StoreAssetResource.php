<?php

namespace App\Filament\Resources\StoreDashboard;

use App\Filament\Resources\Store\StoreResource\StoreAssetResource\Pages\ListStoreAssets;
use App\Filament\Resources\StoreDashboard\StoreAssetResource\Pages\CreateStoreAsset;
use App\Filament\Resources\StoreDashboard\StoreAssetResource\Pages\EditStoreAsset;
use App\Models\StoreAsset;
use App\Traits\Ownership;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull()
                    ->autocapitalize('words'),

                Textarea::make('message')
                    ->label('Deskripsi')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(244),

                TextInput::make('amount')
                    ->label('Jumlah')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->prefix('RP'),

                Select::make('type')
                    ->label('Jenis Inputan')
                    ->options([
                        'in'    =>  'Kas Masuk',
                        'out'   =>  'Kas keluar',
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Judul')->searchable(),

                TextColumn::make('message')->label('Deskripsi')->limit(120)->searchable(),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                    })
                    ->searchable()
                    ->badge(),

                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->searchable()
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp. '),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Tampilkan'),
                Tables\Actions\EditAction::make(),
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

    public static function canAccess(): bool
    {
        return cek_store_role();
    }
}
