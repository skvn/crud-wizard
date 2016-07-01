<?php namespace Skvn\CrudWizard;

use Illuminate\Support\ServiceProvider as LServiceProvider;


class ServiceProvider extends LServiceProvider {


    public function boot()
    {

        //Messages
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'wizard');

        //Assets
        $this->publishes([__DIR__ . '/../public/' => public_path() . "/vendor/crud-wizard/"], 'assets');

        //Views
        $this->loadViewsFrom(__DIR__.'/../views', 'crud-wizard');

        // Routing
        include __DIR__ . DIRECTORY_SEPARATOR . 'routes.php';

    }


    public function register()
    {
        // TODO: Implement register() method.
    }
}