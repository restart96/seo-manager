<?php
/**
 * @author SJ
 * @date   2019.12.03
 */

namespace Restart\SeoManager\App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;
use Restart\SeoManager\App\SeoManager;

class SeoManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        $root = __DIR__.'/../..';

        $this->loadRoutesFrom($root.'/routes/seo-manager.php');
        $this->loadMigrationsFrom($root.'/database/migrations');
        $this->loadViewsFrom($root.'/resources/views', 'seo-manager');

        $this->publishes([
            $root.'/config/seo-manager.php' => config_path('seo-manager.php'),
        ], 'config');

        $this->publishes([
            $root.'/assets' => public_path('assets/seo-manager'),
        ], 'assets');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $root = __DIR__.'/../..';

        $this->mergeConfigFrom(
            $root.'/config/seo-manager.php', 'seo-manager'
        );

        $this->app->bind('seomanager', function() {
            return new SeoManager();
        });

        $this->app->alias('seomanager', SeoManager::class);
    }
}
