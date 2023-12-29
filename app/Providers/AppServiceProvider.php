<?php

namespace App\Providers;

use App\Models\Payment;
use App\Observers\PaymentObserver;
use Filament\Tables\Columns\Column;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('path.public', function() {
            return realpath(base_path().'/../public_html');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Column::configureUsing(function (Column $column): void {
            $column
                ->searchable()
                ->toggleable()
                ->sortable();
        });
    }
}
