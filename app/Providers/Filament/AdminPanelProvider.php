<?php

namespace App\Providers\Filament;

use App\Http\Middleware\AdminUser;
use App\Filament\Pages\DealershipDashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Navigation;
use Z3d0X\FilamentFabricator\FilamentFabricatorPlugin;
use Filament\Panel;
use App;
use App\Filament\Auth;
use App\Filament\Auth\Login;
use App\Filament\Pages\CustomBackupPage;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Contracts\Support\Htmlable;

use App\Filament\Widgets as MyWidgets;
use App\Filament\Widgets\UnitStocksOverview;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class AdminPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard("admin")
            ->login(Login::class)
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                //Pages\Dashboard::class,
                DealershipDashboard::class,
            ])
            ->userMenuItems([
                Navigation\MenuItem::make()
                    ->label('Manage User')
                    ->url(fn (): string => UserResource::getUrl())
                    ->icon('heroicon-o-cog-6-tooth'),
                // ...
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                MyWidgets\AmortizationRevenueSummary::class,
                // Widgets\AccountWidget::class,
                UnitStocksOverview::class,
                MyWidgets\CustomerDues::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,  
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                // AdminUser::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentFabricatorPlugin::make(),
                FilamentSpatieLaravelBackupPlugin::make()->usingPage(CustomBackupPage::class)
            ])
            ->databaseNotifications()->databaseNotificationsPolling('1s');
    }
}
