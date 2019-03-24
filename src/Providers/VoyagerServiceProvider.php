<?php

namespace Modules\Admin\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Arrilot\Widgets\ServiceProvider as WidgetServiceProvider;
use Intervention\Image\ImageServiceProvider;
use Larapack\DoctrineSupport\DoctrineSupportServiceProvider;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;
use TCG\Voyager\Providers\VoyagerEventServiceProvider;

class VoyagerServiceProvider extends \TCG\Voyager\VoyagerServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->register(VoyagerEventServiceProvider::class);
        $this->app->register(ImageServiceProvider::class);
        $this->app->register(DoctrineSupportServiceProvider::class);
        $this->app->register(WidgetServiceProvider::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('Voyager', VoyagerFacade::class);

        $this->app->singleton('voyager', function () {
            return new \Modules\Admin\Voyager\Voyager();
        });

        $this->loadHelpers();
        $this->registerAlertComponents();
        $this->registerFormFields();
        $this->registerConfigs();

        if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
            $this->registerConsoleCommands();
        }

        if (!$this->app->runningInConsole() || config('app.env') == 'testing') {
            $this->registerAppCommands();
        }
    }

    /**
     * Register the publishable files.
     */
    private function registerPublishableResources()
    {
        $publishablePath = dirname(__DIR__);

        $publishable = [
            'voyager_assets' => [
                "{$publishablePath}/Resources/assets/" => public_path(config('admin.voyager_assets_path', '/vendor/admin/assets')),
            ],
            'seeds' => [
                "{$publishablePath}/Database/seeds/" => database_path('seeds'),
            ],
            'content' => [
                "{$publishablePath}/Publishable/dummy_content/" => storage_path('app/public'),
            ],
            'config' => [
                "{$publishablePath}/Config/voyager.php" => config_path('voyager.php'),
                "{$publishablePath}/Config/imagecache.php" => config_path('imagecache.php'),
                "{$publishablePath}/Config/config.php" => config_path('admin.php'),
            ],
            'migrations' => [
                "{$publishablePath}/Database/migrations/" => database_path('migrations'),
            ],

        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    public function registerConfigs()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/voyager.php', 'voyager'
        );
    }

    /**
     * Register the commands accessible from the Console.
     */
    private function registerConsoleCommands()
    {
        $this->commands(\Modules\Admin\Console\Voyager\InstallCommand::class);
    }

    /**
     * Register the commands accessible from the App.
     */
    private function registerAppCommands()
    {
        $this->commands(\TCG\Voyager\Commands\MakeModelCommand::class);
    }
}