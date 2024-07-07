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

        $products_name  =   [
            'Hammer', 'Nails', 'Screwdriver', 'Wrench', 'Pliers', 'Measuring Tape', 'Utility Knife',
            'Level', 'Saw', 'Drill', 'Sandpaper', 'Paint Brush', 'Paint Roller', 'Drop Cloth',
            'Ladder', 'Safety Glasses', 'Work Gloves', 'Dust Mask', 'Ear Protection', 'Screws',
            'Bolts', 'Nuts', 'Washers', 'Anchor Bolts', 'Hinges', 'Locks', 'Padlocks', 'Chains',
            'Rope', 'Bungee Cords', 'Extension Cord', 'Power Strip', 'Flashlight', 'Batteries',
            'Light Bulbs', 'Duct Tape', 'Electrical Tape', 'Painter’s Tape', 'Super Glue',
            'Wood Glue', 'Epoxy', 'Caulking Gun', 'Caulk', 'Pipe Wrench', 'Plumber’s Tape',
            'PVC Pipe', 'Copper Pipe', 'Pipe Fittings', 'Toilet Plunger', 'Sink Strainer'
        ];

        $products_frac  =   [
            6,8,24,48,124,80,248,2,1
        ];

        $pruduct_cost   =   $fake->randomFloat(2, 5000, 999300);

        $sale_price     =   $pruduct_cost * $fake->randomFloat(2, 1, 3);

        return [
            'name'  => $fake->randomElement($products_name),
            'sku'   => $fake->unique()->numerify('SKU###'),
            'product_cost' => $pruduct_cost,
            'sale_price' => $sale_price,
            'stock' => $fake->numberBetween(0, 100),
            'fraction' => $fake->randomElement($products_frac),
            'unit' => $fake->randomElement(['carton', 'pack', 'piece', 'box', 'bag', 'set', 'bottle', 'jar', 'roll', 'case', 'pallet', 'bundle', 'liter', 'milliliter', 'kilogram', 'gram']),
            'supplier' => $fake->company,
        ];
    }
}
