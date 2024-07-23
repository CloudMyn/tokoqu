<?php

namespace App\Filament\Resources\StoreDashboard\AdjustStockResource\Pages;

use App\Filament\Exports\AdjustStockExporter;
use App\Filament\Resources\StoreDashboard\AdjustStockResource;
use App\Filament\Widgets\AdjustOverview;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListAdjustStocks extends ListRecords
{
    protected static string $resource = AdjustStockResource::class;

    protected static ?string $title = 'Penyesuaian Stok';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Input Penyesuaian'),
            Actions\ExportAction::make()->exporter(AdjustStockExporter::class)->label('Eksport Data'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AdjustOverview::class
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make(),
            'Adjust Plus' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'plus')),
            'Adjust Minus' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'minus')),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'semua';
    }
}
