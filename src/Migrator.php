<?php

namespace Skvn\CrudWizard;

use Illuminate\Http\Request;
use Skvn\Crud\Contracts\WizardableField;
use Skvn\Crud\Exceptions\WizardException;
use Skvn\Crud\Form\Form;

class Migrator
{
    public $error;

    private $request;
    private $app;
    private $existing;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
        $this->app = app();
        $this->app['view']->addNamespace('crud_wizard', __DIR__.'/../../stubs');
        $this->existing = (new Wizard())->getTables();
    }

    private function checkTableExists($table)
    {
        if (in_array($table, $this->existing)) {
            return true;
        }

        return false;
    }

    public function createTable($table = null)
    {
        if (! $table) {
            $table = $this->request->get('table_name');
        }
        if (empty($table)) {
            throw new WizardException('No table name specified');
        }

        if (! $this->checkTableExists($table)) {
            $migration = [
                'table_name' => $table,
                'class'      => 'Create'.studly_case($table).'Table',
            ];

            $path = base_path().'/database/migrations/'.date('Y_m_d_His').
                '_create_'.$table.'_table.php';

            file_put_contents($path,
                $this->app['view']->make('crud_wizard::migrations/create_table', compact('migration'))->render()
            );
        }

        return $this;
    }

    public function createPivotTable($data)
    {
        if (! $this->checkTableExists($data['table_name'])) {
            $data['class'] = 'Create'.studly_case($data['table_name']).'PivotTable';
            $path = base_path().'/database/migrations/'.date('Y_m_d_His').
                '_create_'.$data['table_name'].'_pivot_table.php';

            $stub = file_get_contents(__DIR__.'/../views/stubs/migrations/pivot.stub');
            foreach ($data as $k => $v) {
                $stub = str_replace('['.strtoupper($k).']', $v, $stub);
            }
            file_put_contents($path, $stub);

            return true;
        }

        return false;
    }

    public function appendColumns($table, $cols)
    {
        $migration_up = '';
        $migration_down = '';
        if (is_array($cols)) {
            $class = 'Alter'.studly_case($table).'Table'.md5($table.implode(',', array_keys($cols)));
            if ($this->checkMigrationName($class)) {
                $stub = file_get_contents(__DIR__.'/../views/stubs/migrations/alter_table.stub');
                $stub = str_replace('[CLASS]', $class, $stub);
                $stub = str_replace('[TABLE_NAME]', $table, $stub);

                foreach ($cols as $col_name => $col_type) {
                    $migration_up .= '$table->'.$col_type."('".$col_name."');\n            ";
                    $migration_down .= "\$table->dropColumn('".$col_name."');\n            ";
                }

                $stub = str_replace('[MIGRATION_UP]', $migration_up, $stub);
                $stub = str_replace('[MIGRATION_DOWN]', $migration_down, $stub);

                $path = base_path().'/database/migrations/'.date('Y_m_d_His').
                    '_'.snake_case($class).'.php';

                file_put_contents($path, $stub);

                return true;
            }
        }

        return false;
    }

    public function getColumDbTypeByEditType($type)
    {
        $control = Form::getControlByType($type);

        return $control instanceof WizardableField ? $control->wizardDbType() : 'unknown';
    }

    private function checkMigrationName($class_name)
    {
        $file_name = snake_case($class_name);
        $all_migrations = \File::allFiles(base_path().'/database/migrations');
        foreach ($all_migrations as $file) {
            if (strpos($file->getBasename(), $file_name) !== false) {
                return false;
            }
        }

        return true;
    }

    public function migrate()
    {

        //if (empty($this->error) && $this->isMigrateAllowed()) {
        try {
            \Artisan::call('migrate', ['--force' => true, '--quiet' => true]);
        } catch (\Exception $e) {
            return false;
        }
        //}

        return true;
    }

//    private function isMigrateAllowed()
//    {
//        if ($this->app['config']['crud_common.auto_migrate_allowed'])
//        {
//            return true;

//        } else {
//            $this->error = "Running automatic migrations is prohibited by your configuration file. <br>Please, <b>run php artisan migrate</b> from the command line";
//            return false;
//        }
//    }
}
