<?php

namespace Modules\Admin\Console;

use Illuminate\Console\Command;

class DefaultStringCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix default string length error';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Checking whether AppServiceProvider has Schema::defaultSringLength');

        if (file_exists(app_path('Providers\AppServiceProvider.php'))) {
            
            $str = file_get_contents(app_path('Providers\AppServiceProvider.php'));  

            if($str !== false && !str_contains($str, 'Schema::defaultStringLength(191)')) {

                file_put_contents(app_path('Providers\AppServiceProvider.php'), file_get_contents(__DIR__.'/stubs/app-service-provider.stub'));
                
                $this->info('Schema::defaultSringLength has been set. You can now run "php artisan admin:install"');

                exit;
            }

            //$this->info('Schema::defaultSringLength(191) has been set');
        }
    }
}
