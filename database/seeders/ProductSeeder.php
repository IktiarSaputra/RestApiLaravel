<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product = [
            [
                'uuid' => Str::uuid(),
                'name' => 'Product 1',
                'type' => 'type 1',
                'price' => 100,
                'quantity' => 10
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Product 2',
                'type' => 'type 2',
                'price' => 100,
                'quantity' => 7
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Product 3',
                'type' => 'type 3',
                'price' => 160,
                'quantity' => 16
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Product 4',
                'type' => 'type 4',
                'price' => 140,
                'quantity' => 8
            ],
        ];
        foreach ($product as $key => $value) {
            Product::create($value);
        }
    }
}
