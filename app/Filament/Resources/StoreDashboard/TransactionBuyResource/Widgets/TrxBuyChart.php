<?php

namespace App\Filament\Resources\StoreDashboard\TransactionBuyResource\Widgets;

use Filament\Widgets\ChartWidget;

class TrxBuyChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Pembelian Barang';

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
        $periodicData = $store->getBuysAndCosts($period);

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

            $amountData[] = $periodicData[$label]['total_cost'] ?? 0.00;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Transaksi Pembelian',
                    'data' => $amountData,
                    'backgroundColor' => 'rgba(0, 123, 255, 0.3)',
                    'borderColor' => '#007bff',
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
