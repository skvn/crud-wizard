<?php

namespace Skvn\CrudWizard;

use Skvn\Crud\Contracts\FormControlFilterable;
use Skvn\Crud\Form\Field;
use Skvn\Crud\Form\Form;
use Skvn\Crud\Models\CrudModel;
use Skvn\CrudWizard\Contracts\WizardableField;
use Skvn\CrudWizard\Exceptions\WizardException;

/**
 * Class Wizard.
 *
 * @author Vitaly Nikolenko <vit@webstandart.ru>
 */
class Wizard
{
    protected static $controls = [];

    /**
     * @var null bool|null
     */
    private $is_models_defined = null;
    /**
     * @var
     */
    private $app;
    /**
     * @var
     */
    private $model_configs;
    /**
     * @var
     */
    private $available_models;
    /**
     * @var
     */
    private $table_columns;

    /**
     * @var
     */
    private $table_int_columns;

    /**
     * @var
     */
    private $crud_configs;
    /**
     * @var
     */
    private $table_column_types;

    /**
     * @var string Path to crud config storage directory
     */
    public $config_dir_path = '';

    /**
     * @var string Path to model directory
     */
    public $model_dir_path = '';

    /**
     * @var array
     */
    public $table_models;

    /*
     * @var array array of tables not showing in wizard model list
     */
    private $skip_tables = ['users', 'password_resets', 'migrations'];

    public static $relationList = [
        'hasOne',
        'hasMany',
        'belongsTo',
        'belongsToMany',
        'hasFile',
        'hasManyFiles',
    ];

    /**
     * Wizard constructor.
     */
    public function __construct()
    {
        $this->app = app();
        $this->config_dir_path = config_path('crud');

        $folderExpl = explode('\\', $this->app['config']['crud_common.model_namespace']);
        $folder = $folderExpl[(count($folderExpl) - 1)];
        $this->model_dir_path = app_path($folder);
    }

    /**
     * Run all checks.
     */
    public function getCheckAlert($model = null)
    {
        //        if (!$this->checkConfigDir())
//        {
//           return 'Config directory "'.$this->config_dir_path.'" is not writable';
//        }

        if (!$this->checkConfigDir()) {
            return 'Config directory "'.$this->config_dir_path.'" is not writable';
        }

        if (!$this->checkModelsDir()) {
            return 'Models directory "'.$this->model_dir_path.'" is not writable';
        }

        if (!$this->checkMigrationsDir()) {
            return 'Migrations directory "'.base_path().'/database/migrations" is not writable';
        }

        if ($model) {
            if (!$this->checkUnsupportedConfig($model)) {
                return 'Model config contains data which is not supported yet by the Wizard';
            }
        }
    }

    /**
     * Check for unsupported strcutures in config.
     *
     * @param $model
     *
     * @return bool
     */
    public function checkUnsupportedConfig($model)
    {
        //        if (!empty($model['scopes']))
//        {
//            foreach ($model['scopes'] as $list_alias=>$list_arr)
//            {
//                if (isset($list_arr['form']) || isset($list_arr['tabs']) )
//                {
//                    return false;
//                }
//                foreach ($list_arr as $list_col)
//                {
//                    if (isset($list_col['format']))
//                    {
//                        return false;
//                    }
//                }
//            }
//        }

        return true;
    }

    /**
     * Check if config directory is writable.
     *
     * @return bool
     */
    public function checkConfigDir()
    {
        return is_dir($this->config_dir_path) && is_writable($this->config_dir_path);
    }

    /**
     * Check if models directory is writable.
     *
     * @return bool
     */
    public function checkModelsDir()
    {
        return is_dir($this->model_dir_path) && is_writable($this->model_dir_path);
    }

    /**
     * @return bool Check if migrations directory is writable
     */
    public function checkMigrationsDir()
    {
        return is_dir(base_path().'/database/migrations') && is_writable(base_path().'/database/migrations');
    }

    /**
     * Return db tables.
     *
     * @return array
     */
    public function getTables($for_index = false)
    {
        $this->app['db']->setFetchMode(\PDO :: FETCH_ASSOC);
        $tables = $this->app['db']->select('SELECT  table_name FROM   information_schema.tables WHERE   table_type = \'BASE TABLE\' AND   table_schema=?', [env('DB_DATABASE')]);
        $arr = [];

        foreach ($tables as $table) {
            if (strpos($table['table_name'], 'crud_') !== 0 && strpos($table['table_name'], 'crud_file') === false) {
                if ($for_index && in_array($table['table_name'], $this->skip_tables)) {
                    continue;
                }
                $arr[] = $table['table_name'];
            }
        }

        $return = [];
        foreach ($arr as $k => $table_name) {
            if ($this->getModelConfig($table_name)) {
                $return[] = $table_name;
                unset($arr[$k]);
            }
        }

        $this->app['db']->setFetchMode($this->app['config']->get('database.fetch'));

        return array_merge($return, $arr);
    }

    /**
     * Return columns for a specific table.
     *
     * @param $table
     *
     * @return mixed
     */
    public function getTableColumns($table)
    {
        if (!isset($this->table_columns[$table])) {
            $cols = $this->app['db']->connection()->getSchemaBuilder()->getColumnListing($table);
            sort($cols);
            $this->table_columns[$table] = $cols;
        }

        return $this->table_columns[$table];
    }

    /**
     * Return INT columns for a specific table.
     *
     * @param $table
     *
     * @return mixed
     */
    public function getIntTableColumns($table)
    {
        if (!$this->table_int_columns) {
            $this->table_int_columns = [];
            $col_types = $this->getTableColumnTypes($table);

            foreach ($col_types as $col_name => $col_type) {
                $col_type = strtolower($col_type);

                if (strpos($col_type, 'int') !== false) {
                    $this->table_int_columns[] = $col_name;
                }
            }
        }

        return $this->table_int_columns;
    }

    /**
     * Return table column types in  column=>data_type format.
     *
     * @param $table
     *
     * @return mixed
     */
    public function getTableColumnTypes($table)
    {
        if (!isset($this->table_column_types[$table])) {
            $this->app['db']->setFetchMode(\PDO :: FETCH_ASSOC);

            $cols = $this->app['db']->select('SELECT  COLUMN_NAME, DATA_TYPE FROM   information_schema.COLUMNS WHERE   TABLE_SCHEMA = ? AND TABLE_NAME=?', [env('DB_DATABASE'), $table]);
            foreach ($cols as $col) {
                $this->table_column_types[$table][$col['COLUMN_NAME']] = $col['DATA_TYPE'];
            }
            $this->app['db']->setFetchMode($this->app['config']->get('database.fetch'));
        }

        return $this->table_column_types[$table];
    }

    /**
     * Get crud models already defined.
     *
     * @return array
     */
    public function getAvailableModels($callback = null)
    {
        if (!$this->available_models) {
            $configs = $this->getCrudConfigs();
            if ($configs) {
                $this->available_models = array_keys($configs);
            }
        }

        if ($callback) {
            return array_map($callback, $this->available_models);
        }

        return $this->available_models;
    }

    /**
     * Get crud-model config.
     *
     * @param $table_name Table name
     * @param $force If use cached values or force recreate
     *
     * @return mixed
     */
    public function getModelConfig($table_name, $force = false, $asJSON = true)
    {
        if (!isset($this->model_configs[$table_name]) || $force) {
            if (file_exists(config_path('crud/'.$table_name.'.php'))) {
                $this->model_configs[$table_name] = $this->app['config']->get('crud.'.$table_name);
            } else {
                $this->model_configs[$table_name] = false;
            }
        }

        if ($asJSON) {
            return json_encode($this->model_configs[$table_name], JSON_NUMERIC_CHECK);
        }

        return $this->model_configs[$table_name];
    }

    /**
     * Detect if any crud-models are already configured.
     *
     * @return bool|null
     */
    public function modelsDefined()
    {
        if ($this->is_models_defined === null) {
            $configs = $this->getCrudConfigs();
            if (count($configs) > 1) {
                $this->is_models_defined = true;
            } else {
                $this->is_models_defined = false;
            }
        }

        return $this->is_models_defined;
    }

    /**
     * Get all crud configs.
     *
     * @return array
     */
    private function getCrudConfigs()
    {
        if (!$this->crud_configs) {
            $this->crud_configs = [];
            $this->table_models = [];

            $files = \File::files(config_path('crud'));
            foreach ($files as $file) {
                $table = str_replace(['crud_', '.php'], '', basename($file));
                $cfg = $this->app['config']->get('crud.'.$table);
                if ($cfg && is_array($cfg)) {
                    $cfg['table'] = $table;
                    $this->crud_configs[$cfg['name']] = $cfg;
                    $this->table_models[$table] = $cfg['name'];
                }

//                $this->crud_configs[$cfg['name']] = $cfg;

//                $cfg = include ($file);
//                $table = str_replace(['crud_', '.php'],'', basename($file));
//                $cfg['table'] = $table;
//                $this->crud_configs[$cfg['name']] = $cfg;
            }
        }

        return $this->crud_configs;
    }

    /**
     * Get an array of available form field types.
     *
     * @return array
     */
    public function getAvailableFieldTypes()
    {
        $types = [];
        foreach (self :: getAvailControls() as $control) {
            if ($control instanceof WizardableField) {
                if (!$control->wizardIsForRelationOnly()) {
                    $types[$control->controlType()] = $control->wizardCaption();
                }
            }
        }

        return $types;
    }

    /**
     * Get an array of available filter field types.
     *
     * @return array
     */
    public function getAvailableFilterTypes()
    {
        $types = [];
        foreach (Form :: getAvailControls() as $control) {
            if ($control instanceof WizardableField) {
                if ($control instanceof FormControlFilterable) {
                    $types[$control->controlType()] = $control->wizardCaption();
                }
            }
        }

        return $types;
    }

    /**
     * Get an array of available form field types for a  relation.
     *
     * @return array
     */
    public function getAvailableRelationFieldTypes($relation)
    {
        $types = [];

        foreach (self :: getAvailControls() as $control) {
            if ($control instanceof WizardableField) {
                $rels = $control->wizardIsForRelations();

                if (in_array($relation, $rels)) {
                    $types[$control->controlType()] = $control->wizardCaption();
                }
            }
        }

        return $types;
    }

    /**
     * Get an array of all columns for all crud-models.
     *
     * @param $forModel
     *
     * @return array
     */
    public function getAllModelColumns($forModel = null)
    {
        $ret = [];
        $configs = $this->getCrudConfigs();
        foreach ($configs as $model => $cfg) {
            $ret[snake_case($model)] = $this->getTableColumns($cfg['table']);
            if ($forModel && snake_case($model) == snake_case($forModel)) {
                return $ret[snake_case($model)];
            }
        }

        return $ret;
    }

    /**
     * @return array List of available date formats in php and js forms
     */
    public function getAvailableDateFormats()
    {
        return [
            ['js' => 'dd.mm.yyyy', 'php' => 'd.m.Y'],
            ['js' => 'dd/mm/yyyy', 'php' => 'd/m/Y'],
            ['js' => 'dd-mm-yyyy', 'php' => 'd-m-Y'],
            ['js' => 'mm/dd/yyyy', 'php' => 'm/d/Y'],
            ['js' => 'mm/dd/yy', 'php' => 'm/d/y'],
            ['js' => 'yyyy-mm-dd', 'php' => 'Y-m-d'],

        ];
    }

    /**
     * @return array List of available datetime formats in php and js forms
     */
    public function getAvailableDateTimeFormats()
    {
        return [
            ['js' => 'DD.MM.YYYY', 'php' => 'd.m.Y'],
            ['js' => 'MM/DD/YYYY', 'php' => 'm/d/Y'],
            ['js' => 'YYYY-MM-DD', 'php' => 'Y-m-d'],
            ['js' => 'DD.MM.YYYY HH:mm', 'php' => 'd.m.Y H:i'],
            ['js' => 'MM/DD/YYYY hh:mm A', 'php' => 'm/d/Y h:i A'],
            ['js' => 'YYYY-MM-DD HH:mm', 'php' => 'Y-m-d H:i'],

        ];
    }

    /**
     * @return array List of available locales
     */
    public function getAvailableLocales()
    {
        return [
                'en',
                'ru',

            ];
    }

    /**
     * @return array List of available wysiwyg
     */
    public function getAvailableEditors()
    {
        return  [
            ''           => 'None',
            'summernote' => 'Summernote',
//            'tinymce' => 'TinyMCE',
            'mde' => 'Markdown',
        ];
    }

    /**
     * Return the list of defined relations for the table name.
     *
     * @param $table
     *
     * @return array
     */
    public function getModelRelations($table)
    {
        $config = $this->getModelConfig($table);
        $ret = [];
        if (!empty($config['fields']) && is_array($config['fields'])) {
            foreach ($config['fields'] as $k => $field) {
                if (!empty($field['relation'])) {
                    $field['relation_name'] = $k;
                    $ret[$k] = $field;
                }
            }
        }

        return $ret;
    }

    public function getAllPivotTables()
    {
        $tables = [];
        $configs = $this->getCrudConfigs();
        foreach ($configs as $conf) {
            $rels = $this->getModelRelations($conf['table']);
            foreach ($rels as $relation) {
                if (!empty($relation['pivot_table'])) {
                    $tables[] = $relation['pivot_table'];
                }
            }
        }

        return $tables;
    }

    public function getPossiblePivotTables()
    {
        $all = $this->getTables();
        $pivot = $this->getAllPivotTables();
        $ret = [];
        foreach ($all as $table) {
            if (strpos($table, '_') !== false && !in_array($table, $pivot)) {
                $ret[] = $table;
            }
        }

        return $ret;
    }

    public function getAllLists()
    {
        $ret = [];
        $configs = $this->getCrudConfigs();
        foreach ($configs as $model => $cfg) {
            if (!empty($cfg['list'])) {
                $ret[$model] = $cfg['list'];
            }
        }

        return $ret;
    }

    /**
     * Define if field type is date/date-time.
     *
     * @param $type string Field type
     */
    public function isDateField($type)
    {
        return in_array($type, [Field::DATE_RANGE, Field::DATE, Field::DATE_TIME]);
    }

    /**
     * Return value if the list `data` property is a model attribute not a field name.
     *
     * @param $table
     * @param $data_alias
     *
     * @return string|null
     */
    public function resolveColDataProp($table, $data_alias)
    {
        $conf = $this->getModelConfig($table);
        if ($conf && !empty($conf['fields'])) {
            if (!array_key_exists($data_alias, $conf['fields']) && !strpos($data_alias, '::')) {
                return $data_alias;
            }
        }
    }

    /**
     * Return value if the list `data` property is a relation.
     *
     * @param $table
     * @param $data_alias
     *
     * @return array|null
     */
    public function resolveColDataRelation($data_alias)
    {
        if (strpos($data_alias, '::') !== false) {
            return explode('::', $data_alias);
        }
    }

    /**
     * Get available slect options providers for model.
     *
     * @param $table
     *
     * @return array
     */
    public function getAvailableSelectOptionsProviders($model)
    {
        $obj = CrudModel::createInstance($model);

        return $obj->getAvailOptionGenerators();
    }

    /**
     * Get select options for on_delete select.
     *
     * @return array
     */
    public function getOndeleteActions()
    {
        return [
            ''         => 'No action',
            'delete'   => 'Cascade delete',
            'set_null' => 'Set null',

        ];
    }

    /**
     * Get available options for audit trail.
     *
     * @return array
     */
    public function getTrackHistoryOptions()
    {
        return [
            ''        => 'Do not track',
            'detail'  => 'Detailed',
            'summary' => 'Summary',

        ];
    }

    /**
     * Get available options for tree list.
     *
     * @return array
     */
    public function getAvailableTreeLists()
    {
        return [
            'jstree'  => 'JSTree',
            'dt_tree' => 'DataTables tree grid',
            'dt_flat' => 'Datatables flat grid with breadcrumbs',

        ];
    }

    /**
     * Get list of possible relations.
     *
     * @return array
     */
    public function getRelations()
    {
        return self::$relationList;
    }

    public function getRelationModelData($model)
    {
        return
            [
                'columns'      => $this->getAllModelColumns($model),
                'find_methods' => $this->getAvailableSelectOptionsProviders($model),
            ];
    }

//    public function getFieldsConfig()
//    {
//        $ret = [];
//        foreach (self :: getAvailControls() as $control)
//        {
//            $ret[$control->controlType()] = [

//            ];
//        }

//        return $ret;
//    }

    public function getFieldsConfigDefaults()
    {
        $ret = [];
        foreach (self :: getAvailControls() as $control) {
            $ret[$control->controlType()] = [
                'defaults'       => $control->wizardConfigDefaults(),
                'is_for_virtual' => $control->wizardIsForVirtualOnly(),
                'sections'       => $control->wizardConfigSections(),
                'custom'         => $control->wizardIsCustomField(),

            ];
        }

        return $ret;
    }

    public static function registerControl($class)
    {
        $control = $class :: create();

        if (!$control instanceof WizardableField) {
            throw new WizardException('Invalid control class '.$class);
        }
        if (isset(self :: $controls[$control->controlType()])) {
            throw new WizardException('Control already registered: '.$class);
        }
        self :: $controls[$control->controlType()] = $control;
    }

    public static function getAvailControls()
    {
        return self :: $controls;
    }

    public static function getAvailControl($type)
    {
        if (!isset(self :: $controls[$type])) {
            throw new WizardException('Invalid control `'.$type.'`');
        }

        return self :: $controls[$type];
    }

    public function getModelAccessors($model)
    {
        $obj = CrudModel::createInstance($model);

        return $obj->getMutatedAttributes();
    }

    public function getModelAttributes($model)
    {
        $obj = CrudModel::createInstance($model);
        $cols = array_unique(array_merge($this->getTableColumns($obj->getTable()), $obj->getMutatedAttributes()));
        sort($cols);

        return $cols;
    }

    public function getWizardModelsConfig()
    {
        $tables = $this->getTables(true);
        $pivotTables = $this->getAllPivotTables();
        $allConfigs = $this->getCrudConfigs();
        $tableModels = [];

        foreach ($tables as $t) {
            if (!in_array($t, $pivotTables)) {
                if (isset($this->table_models[$t])) {
                    $tableModels[$t] = $this->table_models[$t];
                } else {
                    $tableModels[$t] = '';
                }
            }
        }

        return [
            'tables' => $tableModels,
        ];
    }

    public function getWizardConfig($table, $include_common = true)
    {
        $modelConfig = $this->getModelConfig($table, false, false);
        $obj = CrudModel::createInstance($modelConfig['name']);

        $config = [
            'model_config' => [
                'table_columns'     => array_combine($this->getTableColumns($table), $this->getTableColumns($table)),
                'table_int_columns' => $this->getIntTableColumns($table),
                'find_methods'      => $this->getAvailableSelectOptionsProviders($modelConfig['name']),
                'attrs'             => array_merge($this->getTableColumns($table), $this->getModelAccessors($modelConfig['name'])),
                'formatters'        => $obj->getAvailFormatters(),
                'scopes'            => $obj->getAvailScopes(),
            ],

            'model' => $modelConfig,
        ];

        if ($include_common) {
            $config['common_config'] =
            [
                'track_history_options' => $this->getTrackHistoryOptions(),
                'acls'                  => $this->app['config']['acl.acls'],
                'relation_options'      => array_combine($this->getRelations(), $this->getRelations()),
                'all_models'            => $this->getAvailableModels('snake_case'),
                'on_delete_actions'     => $this->getOndeleteActions(),
                'pivot_tables'          => $this->getPossiblePivotTables(),
                'field_options'         => $this->getAvailableFieldTypes(),
                'fields_config'         => $this->getFieldsConfigDefaults(),
                'editor_types'          => $this->getAvailableEditors(),
                'date_formats'          => $this->getAvailableDateFormats(),
                'date_time_formats'     => $this->getAvailableDateTimeFormats(),
                'tree_lists'            => $this->getAvailableTreeLists(),

            ];
        }

        return json_encode($config, JSON_NUMERIC_CHECK);
    }

    public function isRelationMultiple($relation)
    {
        if (in_array($relation, ['hasOne', 'belongsTo', 'morhTo'])) {
            return false;
        }

        return true;
    }

    public function saveModel($table, $json)
    {
        $data = json_decode($json, 1);
        $proto = new CrudModelPrototype($table, $data);

        return $proto->record();
    }

    public function runMigrations()
    {
        $migrator = new Migrator();

        return [
            'success' => $migrator->migrate(),
        ];
    }
}
