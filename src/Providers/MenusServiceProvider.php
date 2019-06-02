<?php

declare(strict_types=1);

namespace Rinvex\Menus\Providers;

use Rinvex\Menus\Models\MenuManager;
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;

class MenusServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        // Load resources
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'rinvex/menus');

        // Register core presenters
        $this->app['rinvex.menus.presenters']->put('navbar', \Rinvex\Menus\Presenters\NavbarPresenter::class);
        $this->app['rinvex.menus.presenters']->put('navbar-right', \Rinvex\Menus\Presenters\NavbarRightPresenter::class);
        $this->app['rinvex.menus.presenters']->put('nav-pills', \Rinvex\Menus\Presenters\NavPillsPresenter::class);
        $this->app['rinvex.menus.presenters']->put('nav-tab', \Rinvex\Menus\Presenters\NavTabPresenter::class);
        $this->app['rinvex.menus.presenters']->put('sidebar', \Rinvex\Menus\Presenters\SidebarMenuPresenter::class);
        $this->app['rinvex.menus.presenters']->put('navmenu', \Rinvex\Menus\Presenters\NavMenuPresenter::class);
        $this->app['rinvex.menus.presenters']->put('adminlte', \Rinvex\Menus\Presenters\AdminltePresenter::class);

        // Publish Resources
        ! $this->app->runningInConsole() || $this->publishesViews('rinvex/laravel-menus');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        // Register menus service
        $this->app->singleton('rinvex.menus', MenuManager::class);

        // Register menu presenters service
        $this->app->singleton('rinvex.menus.presenters', function ($app) {
            return collect();
        });
    }
}
