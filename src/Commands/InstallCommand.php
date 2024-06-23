<?php

namespace Og\Cruid\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputOption;
use Og\Cruid\Providers\CruidDummyServiceProvider;
use Og\Cruid\CruidServiceProvider;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cruid:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Cruid Admin package';

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Foundation\Composer
     */
    protected $composer;

    /**
     * Seed Folder name.
     *
     * @var string
     */
    protected $seedFolder;

    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
        $this->composer->setWorkingPath(base_path());

    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production', null],
            ['with-dummy', null, InputOption::VALUE_NONE, 'Install with dummy data', null],
        ];
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }

    public function fire(Filesystem $filesystem)
    {
        return $this->handle($filesystem);
    }

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function handle(Filesystem $filesystem)
    {
        $this->info('Publishing the Cruid assets, database, and config files');

        // Publish only relevant resources on install
        $tags = ['seeders'];

        $this->call('vendor:publish', ['--provider' => CruidServiceProvider::class, '--tag' => $tags]);

        $this->info('Migrating the database tables into your application');
        $this->call('migrate', ['--force' => $this->option('force')]);

        $this->info('Attempting to set Cruid User model as parent to App\User');
        if (file_exists(app_path('User.php')) || file_exists(app_path('Models/User.php'))) {
            $userPath = file_exists(app_path('User.php')) ? app_path('User.php') : app_path('Models/User.php');

            $str = file_get_contents($userPath);

            if ($str !== false) {
                $str = str_replace('extends Authenticatable', "extends \Og\Cruid\Models\User", $str);

                file_put_contents($userPath, $str);
            }
        } else {
            $this->warn('Unable to locate "User.php" in app or app/Models.  Did you move this file?');
            $this->warn('You will need to update this manually.  Change "extends Authenticatable" to "extends \Og\Cruid\Models\User" in your User model');
        }

        $this->info('Adding Cruid routes to routes/web.php');
        $routes_contents = $filesystem->get(base_path('routes/web.php'));
        if (false === strpos($routes_contents, 'Cruid::routes()')) {
            $filesystem->append(
                base_path('routes/web.php'),
                PHP_EOL.PHP_EOL."Route::group(['prefix' => 'admin'], function () {".PHP_EOL."    Cruid::routes();".PHP_EOL."});".PHP_EOL
            );
        }

        $publishablePath = dirname(__DIR__).'/../publishable';

        if ($this->option('with-dummy')) {
            $this->info('Publishing dummy content');
            $tags = ['dummy_seeders', 'dummy_content', 'dummy_config', 'dummy_migrations'];
            $this->call('vendor:publish', ['--provider' => CruidDummyServiceProvider::class, '--tag' => $tags]);
        } else {
            $this->call('vendor:publish', ['--provider' => CruidServiceProvider::class, '--tag' => ['config', 'cruid_avatar']]);
        }

        $this->info('Dumping the autoloaded files and reloading all new files');
        $this->composer->dumpAutoloads();

        $this->info('Seeding data into the database');
        $this->call('db:seed', ['--class' => 'CruidDatabaseSeeder', '--force' => $this->option('force')]);

        if ($this->option('with-dummy')) {
            $this->info('Migrating dummy tables');
            $this->call('migrate');

            $this->info('Seeding dummy data');
            $this->call('db:seed', ['--class' => 'CruidDummyDatabaseSeeder', '--force' => $this->option('force')]);
        }

        $this->info('Adding the storage symlink to your public folder');
        $this->call('storage:link');

        $this->info('Successfully installed Cruid! Enjoy');
    }

}
