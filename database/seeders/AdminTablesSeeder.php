<?php

declare(strict_types=1);

namespace Database\Seeders;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Illuminate\Database\Seeder;

/**
 * Class AdminTablesSeeder
 * @package Database\Seeders
 */
final class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param string $login
     * @param string $password
     */
    public function run(string $login, string $password): void
    {
        $this->truncate();
        $this->createMenu();
        $this->createPermissions();
        $this->createRoles();
        $this->createAdministrator($login, $password);
    }

    /**
     * Truncate
     */
    private function truncate(): void
    {
        Menu::query()->truncate();
        Role::query()->truncate();
        Permission::query()->truncate();
        Administrator::query()->truncate();
    }

    /**
     * Add default menus.
     */
    private function createMenu(): void
    {
        Menu::query()->insert([
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => 'Dashboard',
                'icon' => 'fa-bar-chart',
                'uri' => '/',
            ],
            [
                'parent_id' => 0,
                'order' => 2,
                'title' => 'Admin',
                'icon' => 'fa-tasks',
                'uri' => '',
            ],
            [
                'parent_id' => 2,
                'order' => 3,
                'title' => 'Users',
                'icon' => 'fa-users',
                'uri' => 'auth/users',
            ],
            [
                'parent_id' => 2,
                'order' => 4,
                'title' => 'Roles',
                'icon' => 'fa-user',
                'uri' => 'auth/roles',
            ],
            [
                'parent_id' => 2,
                'order' => 5,
                'title' => 'Permission',
                'icon' => 'fa-ban',
                'uri' => 'auth/permissions',
            ],
            [
                'parent_id' => 2,
                'order' => 6,
                'title' => 'Menu',
                'icon' => 'fa-bars',
                'uri' => 'auth/menu',
            ],
            [
                'parent_id' => 2,
                'order' => 7,
                'title' => 'Operation log',
                'icon' => 'fa-history',
                'uri' => 'auth/logs',
            ],
            [
                'parent_id' => 0,
                'order' => 3,
                'title' => 'robots.txt',
                'icon' => 'fa-cog',
                'uri' => 'robots-txt',
            ],
        ]);
    }

    /**
     * Create a permissions
     */
    private function createPermissions(): void
    {
        Permission::query()->insert([
            [
                'name' => 'All permission',
                'slug' => '*',
                'http_method' => '',
                'http_path' => '*',
            ],
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'http_method' => 'GET',
                'http_path' => '/',
            ],
            [
                'name' => 'Login',
                'slug' => 'auth.login',
                'http_method' => '',
                'http_path' => "/auth/login\r\n/auth/logout",
            ],
            [
                'name' => 'User setting',
                'slug' => 'auth.setting',
                'http_method' => 'GET,PUT',
                'http_path' => '/auth/setting',
            ],
            [
                'name' => 'Auth management',
                'slug' => 'auth.management',
                'http_method' => '',
                'http_path' => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs",
            ],
        ]);
    }

    /**
     * Create a roles.
     */
    private function createRoles(): void
    {
        Role::query()->create([
            'name' => 'Administrator',
            'slug' => 'administrator',
        ]);

        Role::query()->create([
            'name' => 'Manager',
            'slug' => 'manager',
        ]);

        /** @var \Encore\Admin\Auth\Database\Role $role */
        $role = Role::query()->where('slug', '=', 'administrator')->first();
        $role->permissions()->save(Permission::query()->where('slug', '=', '*')->first());

        /** @var \Encore\Admin\Auth\Database\Menu $menu */
        $menu = Menu::query()->where('title', '=', 'Admin')->first();
        $menu->roles()->save($role);
    }

    /**
     * @param string $login
     * @param string $password
     */
    private function createAdministrator(string $login, string $password): void
    {
        // create a user.
        Administrator::query()->create([
            'username' => $login,
            'password' => \bcrypt($password),
            'name' => 'Administrator',
        ]);

        /** @var \Encore\Admin\Auth\Database\Administrator $administrator */
        $administrator = Administrator::query()->where('username', '=', $login)->first();

        /** @var \Encore\Admin\Auth\Database\Role $role */
        $role = Role::query()->where('slug', '=', 'administrator')->first();

        $administrator->roles()->save($role);
    }
}
