<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    private array $data = [
        [
            'id' => 1,
            'phone' => '70000000000',
            'mindbox_id'=>null,
            'ban' => true,
            'last_time_sent_code' => null
        ],
    ];

    public function run()
    {
        User::truncate();
        User::insert($this->data);
    }
}
