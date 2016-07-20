<?php namespace Skvn\CrudWizard;

use Illuminate\Support\ServiceProvider as LServiceProvider;


class ServiceProvider extends LServiceProvider {


    public function boot()
    {

        //Messages
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'wizard');

        //Config
        $this->publishes([__DIR__ . '/../config/' => config_path() . "/"], 'config');

        //Assets
        $this->publishes([__DIR__ . '/../public/' => public_path() . "/vendor/crud-wizard/"], 'assets');

        //Views
        $this->loadViewsFrom(__DIR__.'/../views', 'crud-wizard');
        //


        // Routing
        include __DIR__ . DIRECTORY_SEPARATOR . 'routes.php';

    }

    protected function registerControls()
    {
        foreach ($this->app['config']->get('crud_wizard')['wizard_controls'] as $class)
        {
            Wizard :: registerControl($class);
        }
    }

    public function register()
    {
        $this->registerControls();
    }
}