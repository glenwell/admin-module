<?php

namespace Modules\Admin\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Intervention\Image\ImageServiceProvider;
use Larapack\DoctrineSupport\DoctrineSupportServiceProvider;
use Larapack\VoyagerHooks\VoyagerHooksServiceProvider;
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
        $this->app->register(VoyagerDummyServiceProvider::class);
        $this->app->register(VoyagerHooksServiceProvider::class);
        $this->app->register(DoctrineSupportServiceProvider::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('Voyager', VoyagerFacade::class);

        $this->app->singleton('voyager', function () {
            return new \TCG\Voyager\Voyager();
        });

        $this->loadHelpers();

        $this->registerAlertComponents();
        $this->registerFormFields();

        $this->registerConfigs();

        if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
            /* Do not show voyager's commands */
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
                "{$publishablePath}/resources/assets/" => public_path(config('voyager.assets_path')),
                "{$publishablePath}/resources/views/voyager/" => resource_path(config('voyager.blade_path')),
            ],
            'seeds' => [
                "{$publishablePath}/database/seeds/" => database_path('seeds'),
            ],
            'config' => [
                "{$publishablePath}/config/voyager.php" => config_path('voyager.php'),
            ],

        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
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