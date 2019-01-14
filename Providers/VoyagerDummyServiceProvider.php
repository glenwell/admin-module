<?php

namespace Modules\Admin\Providers;

use Arrilot\Widgets\ServiceProvider as WidgetServiceProvider;
use Illuminate\Support\ServiceProvider;

class VoyagerDummyServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->register(WidgetServiceProvider::class);

        $this->registerConfigs();

        if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
        }
    }

    /**
     * Register the publishable files.
     */
    private function registerPublishableResources()
    {
        $publishablePath = dirname(__DIR__);

        $publishable = [
            'dummy_content' => [
                "{$publishablePath}/publishable/dummy_content/" => storage_path('app/public'),
            ],
            'dummy_config' => [
                "{$publishablePath}/config/voyager_dummy.php" => config_path('voyager.php'),
                "{$publishablePath}/config/imagecache.php" => config_path('imagecache.php'),
                "{$publishablePath}/config/config.php" => config_path('admin.php'),
            ],
            /* 'dummy_migrations' => [
                "{$publishablePath}/database/migrations/" => database_path('migrations'),
            ], */

        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    public function registerConfigs()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/config/voyager_dummy.php', 'voyager'
        );
    }
}
