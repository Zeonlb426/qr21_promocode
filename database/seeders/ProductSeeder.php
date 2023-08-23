<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    private array $data = [
        ['name' => 'Устройство Ploom', 'short_name' => 'device'],
        ['name' => 'Набор Ploom', 'short_name' => 'bundle'],
    ];

    public function run()
    {
        Product::truncate();
        Product::insert($this->data);
    }
}
