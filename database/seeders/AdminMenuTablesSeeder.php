<?php

declare(strict_types=1);

namespace Database\Seeders;

use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Auth\Database\Role;
use Illuminate\Database\Seeder;

/**
 * Class AdminMenuTablesSeeder
 * @package Database\Seeders
 */
final class AdminMenuTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var \Encore\Admin\Auth\Database\Role $role */
        $role = Role::query()->where('slug', '=', 'administrator')->first();

        /** @var \Encore\Admin\Auth\Database\Menu $menu */
        $menu = Menu::query()->where('uri', '=', 'robots-txt')->first();
        $menu->roles()->save($role);
    }
}
