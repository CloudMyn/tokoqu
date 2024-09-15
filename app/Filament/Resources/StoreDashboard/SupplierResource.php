<?php

namespace App\Filament\Resources\StoreDashboard;

use App\Filament\Resources\StoreDashboard\SupplierResource\Pages;
use App\Filament\Resources\StoreDashboard\SupplierResource\RelationManagers;
use App\Filament\Resources\SupplierResource\RelationManagers\ProductsRelationManager;
use App\Models\Supplier;
use App\Traits\Ownership;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierResource extends Resource
{
    use Ownership;

    protected static ?string $model = Supplier::class;

    protected static ?string $modelLabel = 'Supplier';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Supplier';
    }

    public static function getNavigationGroup(): ?string
    {
        if (cek_admin_role() || cek_store_role()) {
            return 'Data Toko';
        }

        return 'Tabel Pengguna';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([

                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->minLength(3)
                    ->maxLength(100),

                TextInput::make('code')
                    ->label('Kode Supplier')
                    ->disabledOn('edit')
                    ->unique('suppliers', 'code')
                    ->required()
                    ->default(function ($state) {
                        return strtoupper(\Illuminate\Support\Str::random(3) . rand(100, 999));
                    })
                    ->alphaNum()
                    ->length(6),

                TextInput::make('phone')
                    ->numeric()
                    ->prefix('+62'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label('Kode Supplier')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->placeholder('Tidak Tersedia')
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
                //
            ])
            ->actions([
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
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
