<?php

namespace Og\Cruid;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Intervention\Image\ImageServiceProvider;
use Og\Cruid\Events\FormFieldsRegistered;
use Og\Cruid\Facades\Cruid as CruidFacade;
use Og\Cruid\FormFields\After\DescriptionHandler;
use Og\Cruid\Http\Middleware\CruidAdminMiddleware;
use Og\Cruid\Models\MenuItem;
use Og\Cruid\Models\Setting;
use Og\Cruid\Policies\BasePolicy;
use Og\Cruid\Policies\MenuItemPolicy;
use Og\Cruid\Policies\SettingPolicy;
use Og\Cruid\Providers\CruidDummyServiceProvider;
use Og\Cruid\Providers\CruidEventServiceProvider;
use Og\Cruid\Seed;
use Og\Cruid\Translator\Collection as TranslatorCollection;

class CruidServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Setting::class  => SettingPolicy::class,
        MenuItem::class => MenuItemPolicy::class,
    ];

    protected $gates = [
        'browse_admin',
        'browse_bread',
        'browse_database',
        'browse_media',
        'browse_compass',
    ];

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->register(CruidEventServiceProvider::class);
        $this->app->register(ImageServiceProvider::class);
        $this->app->register(CruidDummyServiceProvider::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('OgCruid', CruidFacade::class);

        $this->app->singleton('cruid', function () {
            return new OgCruid();
        });

        $this->app->singleton('CruidGuard', function () {
            return config('auth.defaults.guard', 'web');
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
     * Bootstrap the application services.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router, Dispatcher $event)
    {
        if (config('ogcruiduser.add_default_role_on_register')) {
            $model = Auth::guard(app('CruidGuard'))->getProvider()->getModel();
            call_user_func($model.'::created', function ($user) use ($model) {
                if (is_null($user->role_id)) {
                    call_user_func($model.'::findOrFail', $user->id)
                        ->setRole(config('ogcruiduser.default_role'))
                        ->save();
                }
            });
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cruid');

        $router->aliasMiddleware('admin.user', CruidAdminMiddleware::class);

        $this->loadTranslationsFrom(realpath(__DIR__.'/../publishable/lang'), 'cruid');

        if (config('ogcruiddatabase.autoload_migrations', true)) {
            if (config('app.env') == 'testing') {
                $this->loadMigrationsFrom(realpath(__DIR__.'/migrations'));
            }

            $this->loadMigrationsFrom(realpath(__DIR__.'/../migrations'));
        }

        $this->loadAuth();

        $this->registerViewComposers();

        $event->listen('ogcruidalerts.collecting', function () {
            $this->addStorageSymlinkAlert();
        });

        $this->bootTranslatorCollectionMacros();

        if (method_exists('Paginator', 'useBootstrap')) {
            Paginator::useBootstrap();
        }
    }

    /**
     * Load helpers.
     */
    protected function loadHelpers()
    {
        foreach (glob(__DIR__.'/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Register view composers.
     */
    protected function registerViewComposers()
    {
        // Register alerts
        View::composer('cruid::*', function ($view) {
            $view->with('alerts', CruidFacade::alerts());
        });
    }

    /**
     * Add storage symlink alert.
     */
    protected function addStorageSymlinkAlert()
    {
        if (app('router')->current() !== null) {
            $currentRouteAction = app('router')->current()->getAction();
        } else {
            $currentRouteAction = null;
        }
        $routeName = is_array($currentRouteAction) ? Arr::get($currentRouteAction, 'as') : null;

        if ($routeName != 'ogcruiddashboard') {
            return;
        }

        $storage_disk = (!empty(config('ogcruidstorage.disk'))) ? config('ogcruidstorage.disk') : 'public';

        if (request()->has('fix-missing-storage-symlink')) {
            if (file_exists(public_path('storage'))) {
                if (@readlink(public_path('storage')) == public_path('storage')) {
                    rename(public_path('storage'), 'storage_old');
                }
            }

            if (!file_exists(public_path('storage'))) {
                $this->fixMissingStorageSymlink();
            }
        } elseif ($storage_disk == 'public') {
            if (!file_exists(public_path('storage')) || @readlink(public_path('storage')) == public_path('storage')) {
                $alert = (new Alert('missing-storage-symlink', 'warning'))
                    ->title(__('cruid::error.symlink_missing_title'))
                    ->text(__('cruid::error.symlink_missing_text'))
                    ->button(__('cruid::error.symlink_missing_button'), '?fix-missing-storage-symlink=1');
                CruidFacade::addAlert($alert);
            }
        }
    }

    protected function fixMissingStorageSymlink()
    {
        app('files')->link(storage_path('app/public'), public_path('storage'));

        if (file_exists(public_path('storage'))) {
            $alert = (new Alert('fixed-missing-storage-symlink', 'success'))
                ->title(__('cruid::error.symlink_created_title'))
                ->text(__('cruid::error.symlink_created_text'));
        } else {
            $alert = (new Alert('failed-fixing-missing-storage-symlink', 'danger'))
                ->title(__('cruid::error.symlink_failed_title'))
                ->text(__('cruid::error.symlink_failed_text'));
        }

        CruidFacade::addAlert($alert);
    }

    /**
     * Register alert components.
     */
    protected function registerAlertComponents()
    {
        $components = ['title', 'text', 'button'];

        foreach ($components as $component) {
            $class = 'OG\\Cruid\\Alert\\Components\\'.ucfirst(Str::camel($component)).'Component';

            $this->app->bind("ogcruidalert.components.{$component}", $class);
        }
    }

    protected function bootTranslatorCollectionMacros()
    {
        Collection::macro('translate', function () {
            $transtors = [];

            foreach ($this->all() as $item) {
                $transtors[] = call_user_func_array([$item, 'translate'], func_get_args());
            }

            return new TranslatorCollection($transtors);
        });
    }

    /**
     * Register the publishable files.
     */
    private function registerPublishableResources()
    {
        $publishablePath = dirname(__DIR__).'/publishable';

        $publishable = [
            'cruid_avatar' => [
                "{$publishablePath}/dummy_content/users/" => storage_path('app/public/users'),
            ],
            'seeders' => [
                "{$publishablePath}/database/seeders/" => database_path('seeders'),
            ],
            'config' => [
                "{$publishablePath}/config/ogcruidphp" => config_path('ogcruidphp'),
            ],

        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    public function registerConfigs()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/publishable/config/ogcruidphp',
            'cruid'
        );
    }

    public function loadAuth()
    {
        // DataType Policies

        // This try catch is necessary for the Package Auto-discovery
        // otherwise it will throw an error because no database
        // connection has been made yet.
        try {
            if (Schema::hasTable(CruidFacade::model('DataType')->getTable())) {
                $dataType = CruidFacade::model('DataType');
                $dataTypes = $dataType->select('policy_name', 'model_name')->get();

                foreach ($dataTypes as $dataType) {
                    $policyClass = BasePolicy::class;
                    if (isset($dataType->policy_name) && $dataType->policy_name !== ''
                        && class_exists($dataType->policy_name)) {
                        $policyClass = $dataType->policy_name;
                    }

                    $this->policies[$dataType->model_name] = $policyClass;
                }

                $this->registerPolicies();
            }
        } catch (\PDOException $e) {
            Log::info('No database connection yet in CruidServiceProvider loadAuth(). No worries, this is not a problem!');
        }

        // Gates
        foreach ($this->gates as $gate) {
            Gate::define($gate, function ($user) use ($gate) {
                return $user->hasPermission($gate);
            });
        }
    }

    protected function registerFormFields()
    {
        $formFields = [
            'checkbox',
            'multiple_checkbox',
            'color',
            'date',
            'file',
            'image',
            'multiple_images',
            'media_picker',
            'number',
            'password',
            'radio_btn',
            'rich_text_box',
            'code_editor',
            'markdown_editor',
            'select_dropdown',
            'select_multiple',
            'text',
            'text_area',
            'time',
            'timestamp',
            'hidden',
            'coordinates',
        ];

        foreach ($formFields as $formField) {
            $class = Str::studly("{$formField}_handler");

            CruidFacade::addFormField("OG\\Cruid\\FormFields\\{$class}");
        }

        CruidFacade::addAfterFormField(DescriptionHandler::class);

        event(new FormFieldsRegistered($formFields));
    }

    /**
     * Register the commands accessible from the Console.
     */
    private function registerConsoleCommands()
    {
        $this->commands(Commands\InstallCommand::class);
        $this->commands(Commands\ControllersCommand::class);
        $this->commands(Commands\AdminCommand::class);
    }

    /**
     * Register the commands accessible from the App.
     */
    private function registerAppCommands()
    {
        $this->commands(Commands\MakeModelCommand::class);
    }
}
