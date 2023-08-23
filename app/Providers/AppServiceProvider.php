<?php

declare(strict_types=1);

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;
use L5Swagger\L5SwaggerServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        #debug mode may be enabled only for development mode and specific IP's
        if ($this->app->environment('development')) {
            $ips = \explode(',', config('app.debug_ips'));
            if ($this->app->runningInConsole() || \in_array(\request()->getClientIp(), $ips, true)) {
                \config(['app.debug' => true]);
                $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
                $this->app->register(TelescopeServiceProvider::class);
            }
        }

        if ($this->app->environment(['development', 'local'])) {
            $this->app->register(L5SwaggerServiceProvider::class);
        }

        if ($this->app->environment('local')) {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
