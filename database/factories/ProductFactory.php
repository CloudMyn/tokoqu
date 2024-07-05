<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fake   =   fake('id');

        return [
            'name'  => $fake->name,
            'sku'   => $fake->unique()->numerify('SKU###'),
            'sale_price' => $fake->randomFloat(2, 10, 1000),
            'product_cost' => $fake->randomFloat(2, 10, 1000),
            'stock' => $fake->numberBetween(0, 100),
            'fraction' => $fake->numberBetween(1, 24),
            'unit' => $fake->randomElement(['carton', 'pack', 'piece', 'box', 'bag', 'set', 'bottle', 'jar', 'roll', 'case', 'pallet', 'bundle', 'liter', 'milliliter', 'kilogram', 'gram']),
            'supplier' => $fake->company,
        ];
    }
}
