<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Database\Seeders\AdminTablesSeeder;
use Encore\Admin\Console\InstallCommand;

/**
 * Class AdminInstallCommand
 * @package App\Console\Commands
 */
final class AdminInstallCommand extends InstallCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:install {--login=} {--password=}';

    /**
     * Create tables and seed it.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function initDatabase(): void
    {
        $userModel = \config('admin.database.users_model');

        if ($userModel::count() === 0) {
            \app()->make(AdminTablesSeeder::class)->run($this->option('login'), $this->option('password'));
        }
    }
}
