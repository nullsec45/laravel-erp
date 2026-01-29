<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerModules();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootModules();
    }

    /**
     * Register all modules found in the modules directory
     */
    protected function registerModules(): void
    {
        $modulesPath = base_path('modules');
        
        if (!File::exists($modulesPath)) {
            return;
        }

        $modules = File::directories($modulesPath);

        foreach ($modules as $modulePath) {
            $this->registerModule($modulePath);
        }
    }

    /**
     * Register a single module
     */
    protected function registerModule(string $modulePath): void
    {
        $moduleName = basename($modulePath);
        $moduleConfigPath = $modulePath . '/module.json';
        
        if (!File::exists($moduleConfigPath)) {
            return;
        }

        $moduleConfig = json_decode(File::get($moduleConfigPath), true);
        
        if (!$moduleConfig || !isset($moduleConfig['enabled']) || !$moduleConfig['enabled']) {
            return;
        }

        // Register module service provider if exists
        $serviceProviderPath = $modulePath . '/Providers/' . $moduleName . 'ServiceProvider.php';
        if (File::exists($serviceProviderPath)) {
            $serviceProviderClass = "Modules\\{$moduleName}\\Providers\\{$moduleName}ServiceProvider";
            if (class_exists($serviceProviderClass)) {
                $this->app->register($serviceProviderClass);
            }
        }

        // Auto-load module classes
        $this->app['config']->set("modules.{$moduleName}", $moduleConfig);
    }

    /**
     * Boot modules - register routes, views, etc.
     */
    protected function bootModules(): void
    {
        $modulesPath = base_path('modules');
        
        if (!File::exists($modulesPath)) {
            return;
        }

        $modules = File::directories($modulesPath);

        foreach ($modules as $modulePath) {
            $this->bootModule($modulePath);
        }
    }

    /**
     * Boot a single module
     */
    protected function bootModule(string $modulePath): void
    {
        $moduleName = basename($modulePath);
        $moduleConfig = $this->app['config']->get("modules.{$moduleName}");
        
        if (!$moduleConfig || !$moduleConfig['enabled']) {
            return;
        }

        // Register module routes
        $routesPath = $modulePath . '/routes/web.php';
        if (File::exists($routesPath)) {
            Route::middleware('web')
                ->prefix(strtolower($moduleName))
                ->group($routesPath);
        }

        $apiRoutesPath = $modulePath . '/routes/api.php';
        if (File::exists($apiRoutesPath)) {
            Route::middleware('api')
                ->prefix('api/' . strtolower($moduleName))
                ->group($apiRoutesPath);
        }

        // Register module views
        $viewsPath = $modulePath . '/resources/views';
        if (File::exists($viewsPath)) {
            $this->loadViewsFrom($viewsPath, strtolower($moduleName));
        }

        // Register module migrations
        $migrationsPath = $modulePath . '/database/migrations';
        if (File::exists($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }

        // Auto-register Filament resources
        $this->registerFilamentResources($modulePath, $moduleName);
    }

    /**
     * Auto-register Filament resources from modules
     */
    protected function registerFilamentResources(string $modulePath, string $moduleName): void
    {
        $resourcesPath = $modulePath . '/Filament/Resources';
        
        if (!File::exists($resourcesPath)) {
            return;
        }

        $resourceFiles = File::files($resourcesPath);
        
        foreach ($resourceFiles as $file) {
            if ($file->getExtension() === 'php') {
                $resourceName = $file->getFilenameWithoutExtension();
                $resourceClass = "Modules\\{$moduleName}\\Filament\\Resources\\{$resourceName}";
                
                if (class_exists($resourceClass)) {
                    // The resources will be auto-discovered by Filament
                    // No need for manual registration in Filament v3
                }
            }
        }
    }
}
