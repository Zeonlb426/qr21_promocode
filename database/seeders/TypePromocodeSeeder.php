<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TypePromocode;
use Illuminate\Database\Seeder;

class TypePromocodeSeeder extends Seeder
{
    private array $data = [
        ['name' => 'QR', 'short_name' => 'qr'],
        ['name' => 'Штрихкод', 'short_name' => 'bar'],
        ['name' => 'Символы', 'short_name' => 'symbol'],
        ['name' => 'Показать экран', 'short_name' => 'screen'],
    ];

    public function run()
    {
        TypePromocode::truncate();
        TypePromocode::insert($this->data);
    }
}
