<?php

namespace Modules\Admin\Console\Voyager;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\ImageServiceProviderLaravel5;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Modules\Admin\Providers\VoyagerDummyServiceProvider;
use TCG\Voyager\Traits\Seedable;
use Modules\Admin\Providers\VoyagerServiceProvider;

class InstallCommand extends \TCG\Voyager\Commands\InstallCommand
{
    use Seedable;

    protected $seedersPath = __DIR__.'/../../database/seeds/';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Customized Voyager Admin module';

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function handle(Filesystem $filesystem)
    {
        $this->changeDefaultStringLength();

        $this->info('Publishing the Voyager assets, database, and config files');

        // Publish only relevant resources on install
        $tags = ['voyager_assets', 'seeds'];

        $this->call('vendor:publish', ['--provider' => VoyagerServiceProvider::class, '--tag' => $tags]);
        $this->call('vendor:publish', ['--provider' => ImageServiceProviderLaravel5::class]);

        $this->info('Attempting to set Voyager User model as parent to App\User');
        $this->extendUser();

        $this->info('Migrating the database tables into your application');
        $this->call('migrate');

        $this->info('Dumping the autoloaded files and reloading all new files');

        $composer = $this->findComposer();

        $process = new Process($composer.' dump-autoload');
        $process->setTimeout(null); // Setting timeout to null to prevent installation from stopping at a certain point in time
        $process->setWorkingDirectory(base_path())->run();

        $this->info('Seeding data into the database');
        $this->seed('VoyagerDatabaseSeeder');

        /* if ($this->option('with-dummy')) {
            //
        } else {
            $this->call('vendor:publish', ['--provider' => VoyagerServiceProvider::class, '--tag' => 'config']);
        } */


        //Publish with dummy content by default
        $this->info('Publishing dummy content');
        $tags = ['dummy_seeds', 'dummy_content', 'dummy_config', 'dummy_migrations'];
        $this->call('vendor:publish', ['--provider' => VoyagerDummyServiceProvider::class, '--tag' => $tags]);

        $this->info('Migrating dummy tables');
        $this->call('migrate');

        $this->info('Seeding dummy data');
        $this->seed('VoyagerDummyDatabaseSeeder');

        $this->info('Setting up the hooks');
        $this->call('hook:setup');
        //End of dummy content

        $this->info('Adding the storage symlink to your public folder');
        $this->call('storage:link');

        $this->info('Successfully installed Voyager! Enjoy');
    }

    private function extendUser()
    {
        if (file_exists(app_path('User.php')) || file_exists(app_path('Models\User.php'))) {
            //Give preference to the app\Models\User over app\User
            if(file_exists(app_path('Models\User.php'))) {
                $userModel = 'Models\User.php';
                $str = file_get_contents(app_path($userModel));
            } else {
                $userModel = 'User.php';
                $str = file_get_contents(app_path($userModel));
            }
                

            if ($str !== false && str_contains($str, 'extends Authenticatable')) {
                $str = str_replace('extends Authenticatable', "extends \Modules\Admin\Models\User", $str);

                file_put_contents(app_path($userModel), $str);
            }
        } else {
            $this->warn('Unable to locate "app/User.php" or "app/Models/User".  Did you move this file?');
            $this->warn('You will need to update this manually.  Change "extends Authenticatable" to "extends \Modules\Admin\Models\User" in your User model');
        }
    }

    private function changeDefaultStringLength()
    {
        if ($this->version()) {
            
            $str = file_get_contents(app_path('Providers\AppServiceProvider.php'));  

            if($str !== false && !str_contains($str, 'Schema::defaultStringLength(191)')) {

                file_put_contents(app_path('Providers\AppServiceProvider.php'), file_get_contents(__DIR__.'/../stubs/app-service-provider.stub'));
                
                $this->info('Schema::defaultSringLength has been set. You can now run "php artisan admin:install"');

                exit;
            }
        }
    }

    private function version()
    {
        $pdo     = \DB::connection()->getPdo();

        $version = $pdo->query('select version()')->fetchColumn();

        (float)$version = mb_substr($version, 0, 6);
        
        // mysql >= 5.6 has fulltext index support
        if ($version < '5.7.7') {
            return true;
        }

        return false;
    }
}
