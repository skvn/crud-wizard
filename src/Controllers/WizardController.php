<?php

namespace Skvn\CrudWizard\Controllers;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Skvn\Crud\Exceptions\WizardException;
use Skvn\Crud\Models\CrudModel;
use Skvn\CrudWizard\CrudModelPrototype;
use Skvn\CrudWizard\Migrator;
use Skvn\CrudWizard\Wizard;

class WizardController extends Controller
{
    private $request;
    private $wizard;

    public function __construct(LaravelApplication $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
        $this->helper = $this->app->make('skvn.crud');
        $this->cmsHelper = $this->app->make('skvn.cms');

        $this->wizard = new Wizard();

        $alert = $this->wizard->getCheckAlert();
        if ($alert) {
            view()->share('alert', $alert);
        }

        \View::share('cmsHelper', $this->cmsHelper);
        \View::share('wizard', $this->wizard);
        \View::share('config', $this->app['config']);
    }

    public function index()
    {

//        if ($this->request->isMethod('post'))
//        {
//             return $this->createModels();
//        }
        return view('crud-wizard::new_page', ['wizard' => $this->wizard]);
    }

    public function model($table)
    {
        $model = $this->wizard->getModelConfig($table, false, false);
        if (! $model) {
            $model_name = studly_case(trim($table));
            $proto = new CrudModelPrototype($table, ['name' => $model_name]);
            $proto->record();
        } else {
            $model_name = $model['name'];
        }

        $alert = $this->wizard->getCheckAlert($model);
        if ($alert) {
            view()->share('alert', $alert);
        }

        //$modelObj = CrudModel::createInstance($model['name']);
        return view('crud-wizard::model', [
                'table' => $table,
                'model' => $model_name,
          //      'modelObj'=>$modelObj,
            ]
        );
    }

    public function menu()
    {
        return view('crud-wizard::menu');
    }

    public function createModels()
    {
        $tables = $this->request->input('models');
        foreach ($tables as $table => $model) {
            if (! empty($model)) {
                $model = studly_case(trim($model));
                $proto = new CrudModelPrototype(['name' => $model, 'table' => $table]);
                $proto->record();
            }
        }

        return redirect()->back();
    }

//    function getTableColumns($table)
//    {

//        return $this->wizard->getTableColumns($table);
//    }

//    function getModelColumns($model)
//    {

//        return $this->wizard->getAllModelColumns($model);
//    }

    public function getWizardMethod($method)
    {
        return response($this->wizard->$method(...\Request::get('args', [])))
            ->header('Access-Control-Allow-Origin', 'http://localhost:8080');
    }

//    function getFieldRowTpl($field_name)
//    {
//        return view('crud-wizard::blocks/fields/field_row', ['f'=>$field_name]);
//    }

//    function migrationCreate()
//    {
//        $migrator = new Migrator($this->request);

//        if ($migrator->createTable()->migrate())
//        {
//             return redirect()->back();

//        } else {

//            return redirect()->back()->with('error', $migrator->error);
//        }

//    }

//    function migrationAlter(Request $req)
//    {
//        $table = $req->get('table_name');
//        $columns = $req->get('columns');

//        if (empty($table) || empty($columns))
//        {
//            throw new WizardException('No table name or columns specified');
//        }

//        $options = ['name'=>"add_".$table];
//        $command = "make:migration:schema";
//        \Artisan::call($command, $options);

//        \Artisan::call("migrate", ['--force'=>true,'--quiet'=>true]);

//        return redirect()->back();

//    }
}
