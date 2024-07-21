<?php

namespace App\Providers\Filament;

use App\Filament\Resources\StoreDashboard\TransactionBuyResource\Widgets\TrxBuyChart;
use App\Filament\Resources\StoreDashboard\TransactionSaleResource\Widgets\TrxSaleChart;
use App\Filament\Widgets\AdjustOverview;
use App\Filament\Widgets\AssetsOverview;
use App\Filament\Widgets\ProductsOverview;
use App\Filament\Widgets\StoreOverview;
use App\Filament\Widgets\TrxOverview;
use App\Filament\Widgets\TrxSaleOverview;
use App\Filament\Widgets\WelcomeWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use FilipFonal\FilamentLogManager\FilamentLogManager;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentGeneralSettings\FilamentGeneralSettingsPlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups;
use ShuvroRoy\FilamentSpatieLaravelHealth\Pages\HealthCheckResults;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                WelcomeWidget::class,
                // StoreOverview::class,
                TrxOverview::class,
                TrxSaleChart::class,
                TrxBuyChart::class,
                ProductsOverview::class,
                AssetsOverview::class,
                AdjustOverview::class,
                TrxSaleOverview::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins(array_merge([

                // \ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin::make()->usingPage(HealthCheckResults::class),

                // \TomatoPHP\FilamentArtisan\FilamentArtisanPlugin::make(),

                // \TomatoPHP\FilamentDeveloperGate\FilamentDeveloperGatePlugin::make(),

                // \Outerweb\FilamentTranslatableFields\Filament\Plugins\FilamentTranslatableFieldsPlugin::make(),

                // \Amendozaaguiar\FilamentRouteStatistics\FilamentRouteStatisticsPlugin::make(),

                // \Tapp\FilamentAuthenticationLog\FilamentAuthenticationLogPlugin::make(),

                FilamentGeneralSettingsPlugin::make()
                    ->canAccess(fn () => cek_admin_role())
                    ->setSort(3)
                    ->setIcon('heroicon-o-cog')
                    ->setNavigationGroup('Utilitas')
                    ->setTitle('Pengaturan Umum')
                    ->setNavigationLabel('Pengaturan Umum'),

                FilamentLogManager::make(),

            ], []))
            ->navigationGroups([
                'Tabel Pengguna',
                'Data Toko',
                'Inventori',
                'Transaksi',
                'Utilitas',
            ])
            ->navigationItems([])->globalSearch(false)
            ->databaseNotifications();
    }
    private function getAdditionalPlugins()
    {
        $role = request()->attributes->get('user_role');

        $plugins = [];

        if ($role   === 'admin') {
            $plugins[] = FilamentLogManager::make();
        }

        return $plugins;
    }
}
