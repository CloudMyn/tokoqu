<?php

// app/Filament/Fields/MoneyField.php

namespace App\Filament\Fields;

use Filament\Forms\Components\TextInput;
use Brick\Money\Money;

class MoneyField extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->numeric()
            ->suffix('IDR') // You can change this to any currency symbol or code
            ->formatStateUsing(function ($state) {
                return $this->formatMoney($state);
            });
    }

    protected function formatMoney($amount)
    {
        if ($amount === null) {
            return null;
        }

        // Assuming IDR for this example, you can customize as needed
        $money = Money::of($amount, 'IDR');
        return $money->formatTo('id_ID'); // Format to a specific locale
    }
}
