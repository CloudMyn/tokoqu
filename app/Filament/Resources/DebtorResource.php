<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DebtorResource\Pages;
use App\Filament\Resources\DebtorResource\RelationManagers;
use App\Models\Debtor;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DebtorResource extends Resource
{
    protected static ?string $model = Debtor::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $label = 'Tabel Peminjam';

    public static function getNavigationLabel(): string
    {
        return 'List Peminjam';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Asset';
    }

    public static function canEdit(Model $record): bool
    {
        return true;
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
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required(),

                Forms\Components\TextInput::make('phone')
                    ->label('No. Telp')
                    ->numeric()
                    ->prefix('+62')
                    ->nullable(),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->inputMode('integer')
                    ->required()
                    ->prefix('Rp'),

                Forms\Components\TextInput::make('paid')
                    ->label('Terbayar')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->inputMode('integer')
                    ->required()
                    ->prefix('Rp'),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->default('unpaid')
                    ->options([
                        'paid' => 'Terbayar',
                        'unpaid' => 'Belum Terbayar',
                        'overdue' => 'Terlambat',
                    ]),

                Forms\Components\DatePicker::make('due_date')
                    ->label('Tanggal Jatuh Tempo')
                    ->required()
                    ->minDate(function ($record) {
                        return $record ? null : now();
                    }),

                Forms\Components\Textarea::make('note')
                    ->label('Catatan')
                    ->columnSpanFull()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->limit(30)
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Nomor Telepon')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors(function (string $state) {
                        return [
                            'success'   => 'paid',
                            'warning' => 'unpaid',
                            'danger' => 'overdue',
                        ];
                    })
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('Rp. ')
                    ->sortable(),

                TextColumn::make('paid')
                    ->label('Sudah Dibayar')
                    ->money('Rp. ')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Tanggal Diubah')
                    ->date('Y-m-d')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->date('Y-m-d')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

            ])
            ->filtersFormWidth('xl')
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
            'index' => Pages\ListDebtors::route('/'),
            'create' => Pages\CreateDebtor::route('/create'),
            'edit' => Pages\EditDebtor::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return cek_store_role();
    }
}
