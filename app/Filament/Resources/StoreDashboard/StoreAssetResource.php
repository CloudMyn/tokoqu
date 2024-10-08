<?php

namespace App\Filament\Resources\StoreDashboard;

use App\Filament\Resources\StoreDashboard\StoreAssetResource\Pages\CreateStoreAsset;
use App\Filament\Resources\StoreDashboard\StoreAssetResource\Pages\EditStoreAsset;
use App\Filament\Resources\StoreDashboard\StoreAssetResource\Pages\ListStoreAssets;
use App\Models\StoreAsset;
use App\Traits\Ownership;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StoreAssetResource extends Resource
{
    use Ownership;

    protected static ?string $model = StoreAsset::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationLabel(): string
    {
        return 'Kas Toko';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Asset';
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        parent::getEloquentQuery();

        if (cek_admin_role()) return parent::getEloquentQuery();

        $store   =   get_context_store();

        return parent::getEloquentQuery()->where('store_code', $store?->code)->orderBy('created_at', 'DESC');
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
                    ->required()
                    ->options([
                        'in'    =>  'Kas Masuk',
                        'out'   =>  'Kas keluar',
                        'hold'  =>  'Kas Tertahan',
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Judul')->searchable()->sortable(),

                TextColumn::make('message')->label('Deskripsi')->limit(120)->searchable()->sortable(),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->color(fn(string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        'hold' => 'warning',
                    })
                    ->searchable()
                    ->badge()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->searchable()
                    ->numeric(decimalPlaces: 0)
                    ->prefix('Rp. ')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diupdate Pada')
                    ->searchable()
                    ->sortable()
                    ->date('d F Y')
                    ->toggleable()
                    ->toggledHiddenByDefault(true),

                TextColumn::make('created_at')
                    ->label('Ditambahkan Pada')
                    ->searchable()
                    ->sortable()
                    ->date('d F Y')
                    ->toggleable()
                    ->toggledHiddenByDefault(false),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('created_until')
                            ->label('Hingga Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
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
