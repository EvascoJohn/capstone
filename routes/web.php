<?php


use App\Http\Controllers\ClientLogin;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerLogin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymongoController;
use App\Http\Middleware\CustomerUser;
use App\Mail\CustomerApplicationMail;
use App\Models\Unit;
use App\Models\Report;
use App\Models\Reposession;
use App\Models\UnitModel;
use App\Providers\Filament\AdminPanelProvider;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function(){
    return redirect('/home');
})
->name('home');

// resources\views\components\filament-fabricator\page-blocks\about-page.blade.php

Route::view('/about', 'components.filament-fabricator.page-blocks.about-page');

Route::view('/terms-and-conditions', 'components.filament-fabricator.page-blocks.customer-terms-and-conditions')->name('terms-and-condition');

// Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('pay/{customerApplicationId}', [PaymongoController::class, 'pay'])->name('paymongo');
Route::get('payment-success/{customerApplicationId}', [PaymongoController::class, 'success'])->name('payment-success');
// Route::get('/customer', [CustomerLogin::class, 'index'])->name('customer');
// Route::get('/customer-login', [CustomerLogin::class, 'index'])->name('login');

Route::prefix('/products')->group(function () {
    Route::get('/product-specs/{int:key}', function($key){
        $filamentFabricatorPage = FilamentFabricator::getPageModel()::query()
        ->where('slug', "product-specs")
        ->firstOrFail();

        /** @var ?class-string<Layout> $layout */
        $layout = FilamentFabricator::getLayoutFromName($filamentFabricatorPage?->layout);

        if (! isset($layout)) {
            throw new \Exception("Filament Fabricator: Layout \"{$filamentFabricatorPage->layout}\" not found");
        }

        /** @var string $component */
        $component = $layout::getComponent();

        $unit = UnitModel::query()->find($key);
        $unit->price = '₱' . number_format($unit->price, 2);

        return Blade::render(
            <<<'BLADE'
            <x-dynamic-component
                :component="$component"
                :page="$page"
                :unit="$unit"
            />
            BLADE,
            ['component' => $component, 'page' => $filamentFabricatorPage, 'unit' => $unit]
        );

    });
});

// Route::post('/login', [CustomerLogin::class, 'authenticate']);

// Route::get('customer/login')
// ->name('login')
// ->middleware('auth','auth:web');

// Route::post('/register', [CustomerController::class, 'store']);

// Route::get('/logout', [CustomerController::class, 'destroy'])
// ->name('logout');
