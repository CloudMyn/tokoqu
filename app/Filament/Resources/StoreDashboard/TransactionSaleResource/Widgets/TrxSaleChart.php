<?php

namespace App\Filament\Resources\StoreDashboard\TransactionSaleResource\Widgets;

use Filament\Widgets\ChartWidget;

class TrxSaleChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Penjualan & Keuntungan';

    protected int | string | array $columnSpan = [
        // 'md' => 2,
    ];

    public ?string $filter = 'monthly';

    protected function getFilters(): ?array
    {
        return [
            'daily' => 'Harian',
            'monthly' => 'Bulanan',
            'yearly' => 'Tahunan',
        ];
    }


    protected function getData(): array
    {
        $store = get_context_store();

        $period = $this->filter;

        // Ambil jumlah transaksi dan keuntungan berdasarkan periode
        $periodicData = $store->getSalesAndProfits($period);

        // Tentukan label dan format data berdasarkan periode
        switch ($period) {
            case 'daily':
                $labels = [];

                $lastDayOfMonth = intval(date('t'));

                foreach (range(1, $lastDayOfMonth) as $day) {
                    $labels[] = $day;
                }
                break;
            case 'monthly':
                $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                break;
            case 'yearly':
                $labels = [];
                foreach (range(date('Y') - 10, date('Y')) as $year) {
                    $labels[] = $year;
                }
                break;
        }


        // Buat array dengan jumlah transaksi dan keuntungan per periode, pastikan ada nilai untuk setiap periode
        $profitData = [];
        $amountData = [];

        $loop   =   0;

        $date = date('Y-m');
        $month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));

        foreach ($labels as $label) {

            if ($period === 'daily') {
                $loop++;
                $day = date('d', strtotime($year . '-' . $month . '-' . $loop));
                $label = $year . '-' . $month . '-' . $day;
            }


            if ($period === 'monthly') {
                $label = $loop;
                $loop++;
            }

            $profitData[] = $periodicData[$label]['total_profit'] ?? 0.00;
            $amountData[] = $periodicData[$label]['trx_amount'] ?? 0.00;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Transaksi Penjualan',
                    'data' => $amountData,
                    'backgroundColor' => 'rgba(0, 123, 255, 0.3)',
                    'borderColor' => '#007bff',
                ],
                [
                    'label' => 'Total Profit',
                    'data' => $profitData,
                    'backgroundColor' => 'rgba(40, 167, 69, 0.3)',
                    'borderColor' => '#28a745',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return get_chart_type();
    }

    public static function canView(): bool
    {
        return cek_store_role();
    }
}
