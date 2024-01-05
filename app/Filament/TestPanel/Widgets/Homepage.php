<?php

namespace App\Filament\TestPanel\Widgets;

use Filament\Widgets\Widget;

class Homepage extends Widget
{
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'home-page-redirect';
}
