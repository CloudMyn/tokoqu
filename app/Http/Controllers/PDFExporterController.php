<?php

namespace App\Http\Controllers;

use App\Models\TransactionSale;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFExporterController extends Controller
{
    public function export_sale_report()
    {
        $user   =   get_auth_user();

        // $start_date  =   now()->startOfMonth()->format('Y-m-d');
        // $end_date    =   now()->endOfMonth()->format('Y-m-d');

        $store  =   get_context_store();

        $query_builder  =   $store->transaction_sales()->whereDate('created_at', today());

        $data_repot['TOTAL KEUTUNGAN PENJUALAN']    =   "Rp. " . ubah_angka_int_ke_rupiah($query_builder->sum('total_profit'));
        $data_repot['AVG KEUTUNGAN PENJUALAN']      =   "Rp. " . ubah_angka_int_ke_rupiah($query_builder->avg('total_profit'));
        $data_repot['TOTAL TRANSAKSI PENJUALAN']    =   "Rp. " . ubah_angka_int_ke_rupiah($query_builder->sum('total_amount'));
        $data_repot['AVG TRANSAKSI PENJUALAN']      =   "Rp. " . ubah_angka_int_ke_rupiah($query_builder->avg('total_amount'));
        $data_repot['TOTAL QTY PENJUALAN']          =   $query_builder->sum('total_qty');

        $data_sales =   [];

        $data_sales =   $query_builder->select('id', 'admin_name', 'total_profit', 'total_amount', 'total_qty')->get()->toArray();

        $data   =   [
            'store_name'    =>  $store->name,
            'title'         =>  'Laporan Harian Periode ' . now()->format('F Y'),
            'content'       =>  [
                'Laporan Penjualan Harian'   =>  $data_repot,
            ],
            'tables'        =>  [
                'Daftar Penjualan'    =>  [
                    "kolom"     =>  ['ID', 'Admin', 'Total Keuntungan', 'Total Harga', 'Total QTY'],
                    "data"      =>  $data_sales,
                ]
            ]
        ];

        if ($user->role !== 'store_owner' && $user->role !== 'admin') {

            $data['content']['Data Pengguna'] = [
                'id'        =>  "#" . $user->id,
                'nama'      =>  $user->name,
                'no_hp'     =>  $user->phone_number->phone_number,
                'email'     =>  $user->email,
            ];
        }

        $pdf = Pdf::loadView('reporting.template_list', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream();
    }

    public function export_buy_report()
    {
        $user   =   get_auth_user();

        $start_date  =   now()->startOfMonth();
        $end_date    =   now()->endOfMonth();

        $store  =   get_context_store();

        $query_builder  =   $store->transaction_buys()->whereBetween('created_at', [$start_date, $end_date]);

        $data_repot['TOTAL PEMBELANJAAN']       =   "Rp. " . ubah_angka_int_ke_rupiah($query_builder->sum('total_cost'));
        $data_repot['AVG PEMBELANJAAN']         =   "Rp. " . ubah_angka_int_ke_rupiah($query_builder->avg('total_cost'));
        $data_repot['TOTAL QTY PENJUALAN']      =   $query_builder->sum('total_qty');

        $data_buys =   [];

        $data_buys =   $query_builder->select('id', 'admin_name', 'supplier', 'total_cost', 'total_qty', 'created_at')->get()->toArray();

        $data   =   [
            'store_name'    =>  $store->name,
            'title'         =>  'Laporan Bulanan Periode ' . now()->format('F Y'),
            'content'       =>  [
                'Laporan Pembelian'   =>  $data_repot,
            ],
            'tables'        =>  [
                'Daftar Transaksi Pembelian'    =>  [
                    "kolom"     =>  ['ID', 'Admin', 'Penyuplai', 'Total Transaksi', 'Total QTY', 'TGl'],
                    "data"      =>  $data_buys,
                ]
            ]
        ];

        if ($user->role !== 'store_owner' && $user->role !== 'admin') {

            $data['content']['Data Pengguna'] = [
                'id'        =>  "#" . $user->id,
                'nama'      =>  $user->name,
                'no_hp'     =>  $user->phone_number->phone_number,
                'email'     =>  $user->email,
            ];
        }

        $pdf = Pdf::loadView('reporting.template_list', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream();
    }

    public function export_debtor_report()
    {
        $user   =   get_auth_user();

        $store  =   get_context_store();

        $query_builder  =   $store->debtors();

        $tot_all     =   $query_builder->where('status', '!=', 'paid')->sum('amount');
        $tot_paid    =   $query_builder->sum('paid');

        $data_repot['TOTAL PINJAMAN ( BELUM LUNAS )']     =   "Rp. " . ubah_angka_int_ke_rupiah($tot_all - $tot_paid);
        $data_repot['TOTAL PINJAMAN ( LUNAS )']           =   "Rp. " . ubah_angka_int_ke_rupiah($tot_paid);

        $data_debtors =   [];

        $data_debtors =   $store->debtors()->select('id', 'name', 'amount', 'paid', 'status', 'created_at')->where('status', '!=', 'paid')->get()->toArray();

        $data   =   [
            'store_name'    =>  $store->name,
            'title'         =>  'Laporan Bulanan Periode ' . now()->format('F Y'),
            'content'       =>  [
                'Laporan Peminjaman'   =>  $data_repot,
            ],
            'tables'        =>  [
                'Daftar Peminjam'    =>  [
                    "kolom"     =>  ['ID', 'Nama', 'Jumlah', 'Terbayar', 'Status', 'Tanggal'],
                    "data"      =>  $data_debtors,
                ]
            ]
        ];

        if ($user->role !== 'store_owner' && $user->role !== 'admin') {

            $data['content']['Data Pengguna'] = [
                'id'        =>  "#" . $user->id,
                'nama'      =>  $user->name,
                'no_hp'     =>  $user->phone_number->phone_number,
                'email'     =>  $user->email,
            ];
        }

        $pdf = Pdf::loadView('reporting.template_list', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream();
    }
}
