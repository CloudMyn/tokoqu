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
        return [
            Tour::make('dashboard')
                ->colors('dark', 'dark')
                ->ignoreRoutes(false)
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
                        ->title('Menu Dashboard')
                        ->description('Saat ini anda berada di menu Dashboard, disini anda dapat melihat segala rankuman laporan terkait toko anda!'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > aside > nav > ul > li:nth-child(2)')
                        ->title('Menu Data Toko')
                        ->description('Di menu berikut anda dapat menambahkan atau mengubah data yang terkait toko anda, seperti informasi toko & data karyawan toko'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > aside > nav > ul > li:nth-child(3)')
                        ->title('Menu Inventori')
                        ->description('Di menu berikut anda dapat menambahkan atau mengubah produk & melakukan penyesuaian stock produk terkait!'),

                    Step::make('body > div.fi-layout.flex.min-h-screen.w-full.flex-row-reverse.overflow-x-clip > aside > nav > ul > li:nth-child(4)')
                        ->title('Menu Transaksi')
                        ->description('Di menu berikut anda dapat melakukan pencatatan transaksi seperti membeli atau menjual produk & juga dapat menambahkan kas toko'),
                ),
        ];
    }
}
