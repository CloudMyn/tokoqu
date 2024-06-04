<?php

namespace App\Filament\Resources\OwnerStoreResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PhoneNumberRelationManager extends RelationManager
{
    protected static string $relationship = 'phone_number';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('phone_code')->label('Kode Telepon')->required()->default('+62')->maxLength(5),
                TextInput::make('phone_number')->label('Nomor Telepon')->required()->maxLength(15)->tel(),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('phone_number')
            ->columns([
                Tables\Columns\TextColumn::make('phone_code'),
                Tables\Columns\TextColumn::make('phone_number'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
