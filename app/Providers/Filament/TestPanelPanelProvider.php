<?php

namespace App\Providers\Filament;

use App\Filament\Auth\CustomerLogin;
use App\Filament\TestPanel\Widgets\Homepage;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
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

use App\Filament\Auth\Login;
use App\Filament\TestPanel\Pages\Auth\Register;
use App\Filament\Widgets\CustomerDues;
use App\Models\Customer;
use Filament\Pages\Auth\EditProfile;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;

class TestPanelPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('customer')
            ->path('customer')
            ->authGuard('web')
            ->login(CustomerLogin::class)
            ->registration(Register::class)
            ->passwordReset(RequestPasswordReset::class)
            ->emailVerification(EmailVerificationPrompt::class)
            ->topNavigation(true)
            ->profile(EditProfile::class)
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/TestPanel/Resources'), for: 'App\\Filament\\TestPanel\\Resources')
            ->discoverPages(in: app_path('Filament/TestPanel/Pages'), for: 'App\\Filament\\TestPanel\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/TestPanel/Widgets'), for: 'App\\Filament\\TestPanel\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                Homepage::class,
                CustomerDues::class,
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
            ->databaseNotifications();
            
    }
}
