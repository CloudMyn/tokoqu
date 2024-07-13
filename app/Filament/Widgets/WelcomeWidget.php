<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -3;

    protected int | string | array $columnSpan = 2;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::widgets.account-widget';

}
