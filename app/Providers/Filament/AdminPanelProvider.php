<?php

namespace App\Providers\Filament;

use App\Filament\Resources\StoreDashboard\TransactionBuyResource\Widgets\TrxBuyChart;
use App\Filament\Resources\StoreDashboard\TransactionSaleResource\Widgets\TrxSaleChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                TrxSaleChart::class,
                TrxBuyChart::class,
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
            ->plugins([


                // \ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin::make()->usingPage(HealthCheckResults::class),

                // \TomatoPHP\FilamentArtisan\FilamentArtisanPlugin::make(),

                // \TomatoPHP\FilamentDeveloperGate\FilamentDeveloperGatePlugin::make(),

                // \Outerweb\FilamentTranslatableFields\Filament\Plugins\FilamentTranslatableFieldsPlugin::make(),

                // \Amendozaaguiar\FilamentRouteStatistics\FilamentRouteStatisticsPlugin::make(),

                // \Tapp\FilamentAuthenticationLog\FilamentAuthenticationLogPlugin::make(),

                FilamentGeneralSettingsPlugin::make()
                    ->canAccess(fn () => cek_store_role() || cek_admin_role())
                    ->setSort(3)
                    ->setIcon('heroicon-o-cog')
                    ->setNavigationGroup('Utilitas')
                    ->setTitle('Pengaturan Umum')
                    ->setNavigationLabel('Pengaturan Umum'),

                FilamentLogManager::make(),


            ])
            ->databaseNotifications();
    }
}
