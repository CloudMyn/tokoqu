<?php

namespace App\Filament\Pages;

use JibayMcs\FilamentTour\Tour\HasTour;
use JibayMcs\FilamentTour\Tour\Step;
use JibayMcs\FilamentTour\Tour\Tour;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasTour;

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 3,
            'lg' => 2,
            'xl' => 2,
        ];
    }

    public function tours(): array
    {
        if (!cek_store_role()) return [];

        return [
            Tour::make('dashboard')
                ->colors('dark', 'dark')
                ->steps(

                    Step::make()
                        ->title("Selamat Datang Di Website Tokoqu!"),

                    Step::make('.fi-avatar')
                        ->title('Disini adalah akun anda!')
                        ->icon('heroicon-o-user-circle')
                        ->iconColor('primary')
                        ->clickOnNext('.fi-avatar'),

                    Step::make('.fi-dropdown-panel')
                        ->title('Menu Akun')
                        ->description('Disini anda dapat mengubah tema tampilan sesuai dengan keiginan anda!'),

                    Step::make('.fi-dropdown-panel button.fi-dropdown-list-item')
                        ->title('Tombol Keluar')
                        ->description('Tombol untuk logout/keluar dari website!')
                        ->clickOnNext('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > main > div > section > div > div > div'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > main > div > section > div > div > div')
                        ->clickOnNext('nav > button.fi-icon-btn')
                        ->title('Laporan Pengguna')
                        ->description('Dashboard adalah tempat dimana laporan atau statistik berupa chart akan di tampilkan secara menyeluruh'),


                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > aside > nav')
                        ->title('Menu Navigasi')
                        ->description('Disamping ini adalah daftar menu yang tersedia'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > aside > nav > ul > li:nth-child(1)')
                        ->title('Menu Dashboard & Profile')
                        ->description('Saat ini anda berada di menu Dashboard, disini anda dapat melihat segala rankuman laporan terkait toko anda & Anda juga dapat mengubah profil anda'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > aside > nav > ul > li:nth-child(2)')
                        ->title('Menu Data Toko')
                        ->description('Di menu berikut anda dapat menambahkan atau mengubah data yang terkait toko anda, seperti informasi toko & data karyawan toko'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > aside > nav > ul > li:nth-child(3)')
                        ->title('Menu Inventori')
                        ->description('Di menu berikut anda dapat menambahkan atau mengubah produk & melakukan penyesuaian stock produk terkait!'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > aside > nav > ul > li:nth-child(4)')
                        ->title('Menu Transaksi')
                        ->description('Di menu berikut anda dapat melakukan pencatatan transaksi seperti membeli atau menjual produk & juga dapat menambahkan kas toko'),
                )
                ->uncloseable(true)    // Set the 'Next' button label
                ->nextButtonLabel('Selanjutnya')
                ->previousButtonLabel('Kembali')
                ->doneButtonLabel('Seleai'),

            Tour::make('produk.index')
                ->route('/admin/store-dashboard/products/index')
                ->colors('dark', 'dark')
                ->steps(

                    Step::make()
                        ->title("Selamat Datang Di Halaman Daftar Produk")
                        ->description('Berikut adalah pengenalan terkait fitur halaman produk'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > main > div > section > div > div > div.flex.flex-col.gap-y-6 > div')
                        ->title('Daftar Produk')
                        ->description('Berikut adalah tempat untuk menampilkan daftar dari produk di toko anda, dan disini anda dapat melakukan pencarian produk terkait atau melakukan filter dengan kriteria tertentu!'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > main > div > section > div > div > div.flex.flex-col.gap-y-6 > nav')
                        ->title('Tabel Navigasi')
                        ->description('Anda juga dapat melihat barang yang mempunyai stock atau tidak!'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > main > div > section > div > div > div.grid.grid-cols-\[--cols-default\].lg\:grid-cols-\[--cols-lg\].fi-wi.gap-6.fi-page-header-widgets')
                        ->title('Laporan Produk')
                        ->description('Di bagian ini terdapat rankuman laporan mengenai produk di toko anda, Seperti Jumlah Produk, Total QTY keseluruhan & Nilai Stock Keseluruhan!'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > main > div > section > header > div.fi-ac.gap-3.flex.flex-wrap.items-center.justify-start.shrink-0.sm\:mt-7 > a')
                        ->title('Menambahkan Produk')
                        ->description('Untuk menambahkan produk baru, anda dapat menekan tombol ini!')
                        ->clickOnNext('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > main > div > section > header > div.fi-ac.gap-3.flex.flex-wrap.items-center.justify-start.shrink-0.sm\:mt-7 > button'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > main > div > section > header > div.fi-ac.gap-3.flex.flex-wrap.items-center.justify-start.shrink-0.sm\:mt-7 > button')
                        ->title('Eksport Data Produk!')
                        ->description('Untuk melakukan eksport data produk, anda dapat menekan tombol ini!'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > main > div > section > div > div > div.flex.flex-col.gap-y-6 > div > form:nth-child(2) > div > div > div.fixed.inset-0.z-40.overflow-y-auto.cursor-pointer > div > div')
                        ->title('Menu Eksport')
                        ->description('Di sini anda dapat memilih kolom apa saja dari tabel produk yang ingin anda eksport!'),


                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > main > div > section > div > div > div.flex.flex-col.gap-y-6 > div > form:nth-child(2) > div > div > div.fixed.inset-0.z-40.overflow-y-auto.cursor-pointer > div > div > div.fi-modal-footer.w-full.px-6.pb-6 > div > button.fi-btn.relative.grid-flow-col.items-center.justify-center.font-semibold.outline-none.transition.duration-75.focus-visible\:ring-2.rounded-lg.fi-color-custom.fi-btn-color-primary.fi-color-primary.fi-size-md.fi-btn-size-md.gap-1\.5.px-3.py-2.text-sm.inline-grid.shadow-sm.bg-custom-600.text-white.hover\:bg-custom-500.focus-visible\:ring-custom-500\/50.dark\:bg-custom-500.dark\:hover\:bg-custom-400.dark\:focus-visible\:ring-custom-400\/50.fi-ac-action.fi-ac-btn-action')
                        ->title('Tombol Eksport')
                        ->description('Setelah selesai memilih kolom, tekan tombol ini untu memulai eksport data, perlu diperhatikan untuk proses eksport data dapat memakan waktu yang lama tergantung dengan jumlah data yang dieksport!')
                        ->clickOnNext('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > main > div > section > div > div > div.flex.flex-col.gap-y-6 > div > form:nth-child(2) > div > div > div.fixed.inset-0.z-40.overflow-y-auto.cursor-pointer > div > div > div.fi-modal-footer.w-full.px-6.pb-6 > div > button.fi-btn.relative.grid-flow-col.items-center.justify-center.font-semibold.outline-none.transition.duration-75.focus-visible\:ring-2.rounded-lg.fi-btn-color-gray.fi-color-gray.fi-size-md.fi-btn-size-md.gap-1\.5.px-3.py-2.text-sm.inline-grid.shadow-sm.bg-white.text-gray-950.hover\:bg-gray-50.dark\:bg-white\/5.dark\:text-white.dark\:hover\:bg-white\/10.ring-1.ring-gray-950\/10.dark\:ring-white\/20.fi-ac-action.fi-ac-btn-action'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > div > nav > div > div.flex > div:nth-child(1) > button')
                        ->title('Tombol Notifikasi')
                        ->description('Untuk melihat berbagai notifikasi dari sistem atau data terkait eksport anda!')
                        ->clickOnNext('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > div > nav > div > div.flex > div:nth-child(1) > button'),

                    Step::make('')
                        ->title('Membuka Menu Notifikasi...'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > div > nav > div > div.flex > div.fi-modal.inline-block.fi-modal-open > div > div.fixed.inset-0.z-40.cursor-pointer > div > div')
                        ->clickOnNext('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > div.fi-main-ctn.w-screen.flex-1.flex-col.opacity-0 > div > nav > div > div.flex > div.fi-modal.inline-block.fi-modal-open > div > div.fixed.inset-0.z-40.cursor-pointer > div > div > div > div.absolute.end-6.top-6 > button')
                        ->title('Menu Notifikasi')
                        ->description('Setelah produk berhasil di eksport, anda dapat melihat notifikasi di menu ini dan link untuk mendownload data hasil eksport!'),

                )
                ->uncloseable(true)    // Set the 'Next' button label
                ->nextButtonLabel('Selanjutnya')
                ->previousButtonLabel('Kembali')
                ->doneButtonLabel('Seleai'),
        ];
    }
}
