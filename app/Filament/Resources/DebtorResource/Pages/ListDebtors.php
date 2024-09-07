<?php

namespace App\Filament\Resources\DebtorResource\Pages;

use App\Filament\Resources\DebtorResource;
use App\Filament\Resources\DebtorResource\Widgets\DebtsOverview;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListDebtors extends ListRecords
{
    protected static string $resource = DebtorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('export_laporan')
                ->label('Laporan Peminjaman')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->url(route('report.debtor'), true),

            Action::make('cek_overdue')
                ->label('Update Status Telat Bayar')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('danger')
                ->action(function () {
                    $debts  =   \App\Models\Debtor::where('status', 'unpaid')->get();

                    $count_ =   0;

                    foreach ($debts as $debt) {
                        if (\Carbon\Carbon::parse($debt->due_date)->isPast()) {

                            $debt->update([
                                'status' => 'overdue'
                            ]);

                            $count_++;
                        }
                    }

                    Notification::make()
                        ->title('Hasil Pengecekan Pemijaman')
                        ->body('Update ' . $count_ . ' peminjam yang telat membayar.')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DebtsOverview::class
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make(),
            'Lunas' => Tab::make()->modifyQueryUsing(function (Builder $query) {
                return $query->where('status', 'paid');
            }),
            'Belum Lunas' => Tab::make()->modifyQueryUsing(function (Builder $query) {
                return $query->where('status', 'unpaid');
            }),
            'Telat Bayar' => Tab::make()->modifyQueryUsing(function (Builder $query) {
                return $query->where('status', 'overdue');
            }),
        ];
    }
}
