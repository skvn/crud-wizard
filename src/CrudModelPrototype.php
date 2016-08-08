<?php namespace Skvn\CrudWizard;



use Skvn\Crud\Contracts\WizardableField;
use Skvn\Crud\Exceptions\WizardException;
use Skvn\Crud\Form\Field;
use Skvn\Crud\Form\Form;

/**
 * Class CrudModelPrototype
 * @package Skvn\Crud
 * @author Vitaly Nikolenko <vit@webstandart.ru>
 */
class CrudModelPrototype
{


    /**
     * @var array
     */
    protected $config_data;

    /**
     * @var array
     */
    protected $old_config_data;

    /**
     * @var
     */
    protected $app;
    /**
     * @var
     */
    protected $namespace;
    /**
     * @var
     */
    protected $path;
    /**
     * @var string
     */
    protected $config_path;
    /**
     * @var Wizard
     */
    public $wizard;
    /**
     * @var
     */
    protected $table;

    /**
     * @var array  data for migrations
     */
    protected $migrations_data = [];

    /**
     * @var bool Indicator if any migations were created during model recording
     */
    public $migrations_created = false;

    /**
     * @var array Table column types arrays
     */
    public $column_types = [];

    /**
     * @var array Array of columns that should be added to the table
     */
    private $add_fields = [];

    /**
     * @var string Contains error if any
     */
    public $error;


    private $traites = [];

    private $configDefaults = [
        'acl' => "",
        'track_history' => "",

    ];

    private $fieldDefaults = [
        "hint_default" => "",
        "required" => "0",
        "extra" => "",
        "find" => "",
        "editor_type" => "",
        "editor" => "0",
        "min" => "",
        "max" => "",

    ];

    private $listActionDefaults = [
        "command" => "",
        "event" => "",
        "popup" => "",
        "popup_id" => "",
        "class" => "",
        "btn_class" => "",
        "confirm" => "",
        "ifcolumn" => ""
    ];

    private $listColumnDefaults = [
        "format" => "",
        "db_field" => "",
        "width" => "",
        "orderable" => 0,
        "invisible" => 0,
        "searchable" => 0,
        "filterable" => 0
    ];

    /**
     * CrudModelPrototype constructor.
     * @param $config_data
     */
    public function __construct($table, $config_data)
    {

        $this->config_data = $config_data;
        $this->table = $table;

        $this->wizard = new Wizard();
        $this->column_types = $this->wizard->getTableColumnTypes($this->table);
        $this->app = app();
        $this->namespace = ltrim($this->app['config']['crud_common.model_namespace'],'\\');
        //$this->config_data['namespace'] = $this->namespace;
        $folderExpl = explode('\\',$this->namespace);
        $folder = $folderExpl[(count($folderExpl)-1)];
        $this->path = app_path($folder);
        $this->config_path = config_path('crud').'/'.$this->table.'.php';
        @mkdir(dirname($this->config_path));


    }//


    /**
     * Record config and model files
     */
    public  function record()
    {

        $this->processRelations();
        $this->processFields();
//      $this->processLists();
//      $this->processFilters();

        //$this->prepareConfigData();

        $this->recordConfig();
        //$this->recordModels();
        //$this->recordMigrations();
        //$this->migrate();

        return ['success'=>true];

    }

    /**
     * Make fields out of relations data
     */
    private function processRelations()
    {


        foreach ($this->config_data['fields'] as $key=> $rel)
        {
            if (!empty($rel['relation'])) {

                //need to record pivot?
                if ($rel[ 'relation'] == 'belongsToMany' && isset($rel['pivot']) && $rel['pivot'] == '0') {

                    $pdata = [];

                    $tables = [
                        snake_case($this->config_data['name']),
                        snake_case($rel['model'])
                    ];

                    sort($tables);
                    $pdata['table_name'] = implode('_', $tables);
                    $pdata['self_key'] = snake_case($this->config_data['name']) . '_id';
                    $pdata['foreign_key'] = snake_case($rel['model']) . '_id';
                    $this->migrations_data['pivot'][] = $pdata;
                    $rel['pivot_table'] = $pdata['table_name'];
                    $rel['pivot_self_key'] = $pdata['self_key'];
                    $rel['pivot_foreign_key'] = $pdata['foreign_key'];

                    $this->config_data['fields'][$key] = $rel;

                }



//                else if ($rel['relation'] == 'belongsToMany' && isset($rel['pivot']) &&  $rel['pivot'] == '1') {
//                    $rel_arr['pivot_table'] = $rel['pivot_table'];
//                    $rel_arr['pivot_self_key'] = $rel['pivot_self_key'];
//                    $rel_arr['pivot_foreign_key'] = $rel['pivot_foreign_key'];
//                } else {
//                    if (!empty($rel['pivot_table']))
//                    {
//                        $rel_arr['pivot_table'] = $rel['pivot_table'];
//                        $rel_arr['pivot_self_key'] = $rel['pivot_self_key'];
//                        $rel_arr['pivot_foreign_key'] = $rel['pivot_foreign_key'];
//                    }
//                }
//
//                if (!empty($rel['field'])) {
//                    $rel_arr['field'] = $rel['field'];
//                }
//
//                if (!empty($rel['editable'])) {
//                    $rel_arr['editable'] = 1;
//                    if (!empty($rel['find'])) {
//                        $rel_arr['find'] = $rel['find'];
//                    }
//                    $rel_arr['type'] = $rel['type'];
//
//                    if (!empty($rel['required'])) {
//                        $rel_arr['required'] = 1;
//                    }
//
//                }
//
//
//                if (!empty($rel['on_delete'])) {
//
//                    $rel_arr['on_delete'] = $rel['on_delete'];
//                }
//
//
//                if (!empty($rel['sort'])) {
//
//                    if (is_array($rel['sort']) && count($rel['sort'])) {
//                        $rel_arr['sort'] = $rel['sort'];
//                    }
//
//                }

            }

        }


    }//



    /**
     * Process fields data
     */
    private  function processFields()
    {

        $fields = [];
        if (!empty($this->config_data['fields']))
        {

            foreach ($this->config_data['fields'] as $key=> $f)
            {

                $k = $key;
                if (empty($f['relation'])) {

                    if (empty($f['fields']) && empty($f['field']))
                    {
                        if (!isset($this->column_types[$k])) {
                            $this->add_fields[$k] = $f;
                        }

                    } elseif (! empty($f['field']))
                    {
                        if (!isset($this->column_types[$f['field']])) {
                            $this->add_fields[$f['field']] = $f;
                        }
                    }
                    elseif (! empty($f['fields']))
                    {
                        if (!isset($this->column_types[$f['fields'][0]])) {
                            $this->add_fields[$f['fields'][0]] = $f;
                        }
                        if (!isset($this->column_types[$f['fields'][1]])) {
                            $this->add_fields[$f['fields'][1]] = $f;
                        }
                    }
                }

                if (!empty($f['type']))
                {
                    $this->config_data['fields'][$k]['editable'] = 1;

                    //process field config by field
                    if ($control = Form::getControlByType($f['type']))
                    {
                        if ($control instanceof \Skvn\CrudWizard\Contracts\WizardableField) {
                            $control->wizardCallbackFieldConfig($k, $f, $this);
                            $control->wizardCallbackModelConfig($k, $f, $this);
                        }
                    }

                }


            }

        }

    }//



     /**
     * Record configuration file
     */
    protected  function recordConfig()
    {

        $conf = json_encode($this->buildConfig(), JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
        $conf = str_replace(['{', '}'], ['[', ']'], $conf);
        $conf = str_replace('":', '" =>', $conf);
        if (file_exists($this->config_path))
        {
            rename($this->config_path,$this->config_path.'.backup');
        }
        file_put_contents($this->config_path,"<?php \n return ".$conf.";");

    }//

    protected function buildConfig()
    {

        if (!empty($this->config_data['is_tree']))
        {
            if (empty($this->old_config_data['tree'])) {
                $this->old_config_data['tree'] =
                    [
                        'depth_column' => "tree_level",
                        'path_column' => "tree_path",
                        'pid_column' => "tree_pid",
                        'order_column' => "tree_order"
                    ];
            }
        }

//        if (empty($this->config_data['dialog_width']))
//        {
//            $this->config_data['dialog_width'] = 1000;
//        }

        $this->clearDefaults();
        return $this->config_data;
    }

    private function  clearDefaults()
    {
        //main
        foreach ($this->configDefaults as $dk=>$dv) {
            if (isset($this->config_data[$dk]) && $this->config_data[$dk] === $dv) {
                unset ($this->config_data[$dk]);
            }
        }

        //fields
        foreach ($this->config_data['fields'] as $k=>$f) {
            foreach ($this->fieldDefaults as $dk => $dv) {
                if (isset($f[$dk]) && $f[$dk] === $dv) {
                    unset ($this->config_data['fields'][$k][$dk]);
                }
            }
        }

        if (!empty($this->config_data['scopes']) && is_array($this->config_data['scopes'])) {
            foreach ($this->config_data['scopes'] as $sk =>$scope) {
                //list cols
                if (isset($scope['list'])) {
                    if (!count($scope['list'])) {
                        unset($this->config_data['scopes'][$sk]['list']);
                    } else {
                        foreach ($scope['list'] as $ck => $col) {
                            foreach ($this->listColumnDefaults as $dk=>$dv) {
                                if (isset($col[$dk]) && $col[$dk]===$dv) {
                                    unset($this->config_data['scopes'][$sk]['list'][$ck][$dk]);
                                }
                            }
                        }
                    }
                }


                //list actions
                if (isset($scope['list_actions'])) {
                    if (!count($scope['list_actions'])) {
                        unset($this->config_data['scopes'][$sk]['list_actions']);
                    } else {
                        foreach ($scope['list_actions'] as $lak => $action) {
                            foreach ($this->listActionDefaults as $dk=>$dv) {
                                if (isset($action[$dk]) && $action[$dk]===$dv) {
                                    unset($this->config_data['scopes'][$sk]['list_actions'][$lak][$dk]);
                                }
                            }
                        }
                    }
                }
            }

        }


    }



//    /**
//     * Process editable files
//     */
//    private  function processFiles()
//    {
//
//
//        if (!empty($this->config_data['fields'])) {
//
//            foreach ($this->config_data['fields'] as $k => $f) {
//                if (!empty($f['type']) && in_array($f['type'], [Field::FILE, Field::MULTI_FILE, Field::IMAGE]))
//                {
//                    $f['name'] = $f['rel_name'];
//                    $f['field'] = $k;
//                    $f['multi'] = ($f['type'] == Field::MULTI_FILE?1:0);
//                    $this->config_data['fields'][$f['rel_name']] = $f;
//                    unset($this->config_data['fields'][$k]);
//                    $this->addFileField($f);
//                }
//            }
//        }
//
//    }

    /**
     * Add one file field to config data
     * @param $data
     */
//    private function addFileField($data)
//    {
//        if (!isset($this->config_data['traits']))
//        {
//            $this->config_data['traits'] = [];
//        }
//        if (!in_array('ModelAttachmentTrait', $this->config_data['traits']))
//        {
//            $this->config_data['attaches'] = [];
//            $this->config_data['traits'][] = 'ModelAttachmentTrait';
//        }
//
//        if ($data['multi'])
//        {
//            $tables = [
//                snake_case($this->config_data['name']),
//                'crud_file'
//            ];
//
//            sort($tables);
//            $pivot_table  = implode('_', $tables);
//
//            $this->migrations_data['pivot'][] =
//                [
//                    'table_name' => $pivot_table,
//                    'self_key' => snake_case($this->config_data['name']).'_id',
//                    'foreign_key' => 'crud_file_id'
//                ];
//
//
//        } else {
//            $pivot_table = '';
//        }
//        $this->config_data['attaches'][] = ['column'=>$data['name'], 'multi'=>$data['multi'], 'pivot_table'=>$pivot_table];
//        $this->config_data['fields'][$data['name']] = array_merge(['editable'=>1,'type' => ($data['multi']?Field::MULTI_FILE: Field::FILE)], $data);
//    }
//

//
//    /**
//     * Process filters data
//     */
//    private  function processFilters()
//    {
//
//        if (!empty($this->config_data['filters']))
//        {
//            foreach ($this->config_data['filters'] as $list_alias=> $fields)
//            {
//
//                $filter_fields = [];
//                foreach ($fields as $k=>$f) {
//                    if (!empty($f['type'])) {
//
//                        $field = $f;
//                        $field['column'] = $k;
//
//                        //process date
//                        if (!empty($f['type']) && $f['type'] == Field::DATE_RANGE) {
//                            $formats = $this->wizard->getAvailableDateFormats();
//                            $field['format'] = $formats[$f['format']]['php'];
//                            $field['jsformat'] = $formats[$f['format']]['js'];
//                            $field['db_type'] = $this->column_types[$k];
//
//                        }
//
//                        $filter_fields[$k] = $field;
//                    }
//
//                }
//
//            }
//
//        }
//    }//
//
//
//    /**
//     * Prepare lists config
//     *
//     */
//    private function processLists()
//    {
//
//        if (!empty($this->config_data['list']))
//        {
//            foreach ($this->config_data['list'] as $alias=>$list)
//            {
//
//                //sort
//                if (!empty($list['sort']))
//                {
//                    $sort = [];
//                    foreach ($list['sort'] as $row)
//                    {
//                        if (!empty($row['column'])) {
//                            $sort[$row['column']] = $row['order'];
//                        }
//                    }
//
//                    $this->config_data['list'][$alias]['sort'] = $sort;
//                }
//                //form
//                if (empty($list['form_tabs']) && !empty($list['form'])) {
//
//                    $this->config_data['list'][$alias]['form'] = explode(",",$list['form']);
//
//                } else if (!empty($list['form_tabs'])) {
//
//
//                    $form_tabs = json_decode($list['form_tabs'], true);
//                    //var_dump($form_tabs);
//                    unset( $this->config_data['list'][$alias]['form_tabs']);
//                    $tabs = [];
//                    foreach ($form_tabs as $i=>$tab)
//                    {
//                        if (!empty($tab['alias']))
//                        {
//                            $tab_alias = $tab['alias'];
//                        } else {
//                            $tab_alias = 'tab_'.$i;
//                        }
//
//                        $oldTab = $this->old_config_data['scopes'][$alias]['form'][$tab_alias]??[];
//                        unset($oldTab['fields']);
//                        $tabs[$tab_alias] = array_merge(['title'=>$tab['title']],$oldTab);
//                        $tabs[$tab_alias]['fields'] = $tab['fields']??[];
//
//                    }
//
//                    $this->config_data['list'][$alias]['form'] = $tabs;
//                    //$this->config_data['list'][$alias]['tabs'] = $tabs;
//                   // $this->config_data['list'][$alias]['form_tabbed'] = 1;
//
//                }
//
//                $searchable = 0;
//                //columns
//                if (!empty($list['columns']))
//                {
//                    $cols = [];
//                    foreach ($list['columns'] as $k=>$column)
//                    {
//
//                        if (!is_numeric($k))
//                        {
//                            continue;
//                        }
//                        if (!empty($column['data_col']))
//                        {
//                            $column['data'] = $column['data_col'];
//
//
//                        } else if (!empty($column['data_rel']))
//                        {
//                            $column['data'] = $column['data_rel'].'::'.$column['data_rel_attr'];
//                        }
//
//                        if (empty($column['data'] ))
//                        {
//                            continue;
//                        }
//
//                        if (isset($column['data_rel'])) {
//                            unset($column['data_rel']);
//                        }
//                        if (isset($column['data_rel_attr'])) {
//                            unset($column['data_rel_attr']);
//                        }
//                        if (isset($column['data_col'])) {
//                            unset($column['data_col']);
//                        }
//                        if (!empty($column['hint']))
//                        {
//                            $column['hint'] = ['default' => $column['hint']];
//
//                        }
//                        if (!empty($column['searchable']))
//                        {
//                            $searchable = 1;
//                        }
//
//                        $cols[] = $column;
//                    }
//                    $this->config_data['list'][$alias]['searchable'] = $searchable;
//                    unset($this->config_data['list'][$alias]['columns']);
//                    $this->config_data['list'][$alias]['list'] = $cols;
//                }
//
//                //actions
//                if (!empty($list['list_actions'])) {
//                    $actions = [];
//                    foreach ($list['list_actions'] as $k=> $action) {
//
//                        if (!is_numeric($k))
//                        {
//                            unset ($this->config_data['list'][$alias]['list_actions'][$k]);
//                        }
//                        if (is_array($action)&&
//                            (!empty($action['command']) || !empty($action['event'])  || !empty($action['popup'])))
//                        {
//                            $actions[] = $action;
//                        }
//                    }
//
//                    if (count($actions)) {
//                        $this->config_data['list'][$alias]['list_actions'] = $actions;
//                    } else {
//                        unset($this->config_data['list'][$alias]['list_actions']);
//                    }
//                }
//
//                if (empty($this->config_data['is_tree']) && isset($this->config_data['list'][$alias]['list_type']))
//                {
//                    unset($this->config_data['list'][$alias]['list_type']);
//                }
//
//            }
//
//        }
//
//
//    }//
//
//    /**
//     * Prepare config data for recording
//     */
//    private function prepareConfigData()
//    {
//
//
//        if (!empty($this->config_data['track_history'])) {
//            $this->config_data['traits'][] = $this->app['config']['crud_common.history_trait'];
//        }
//
//        if (!empty($this->config_data['is_tree'])) {
//            $this->config_data['traits'][] = $this->app['config']['crud_common.tree_trait'];
//        }
//
//
//    }//
//
//    /**
//     * Record config and model files
//     */
//    public  function record()
//    {
//
//        $this->recordConfig();
//        $this->recordModels();
//        $this->recordMigrations();
//        $this->migrate();
//
//        return ['success'=>true];
//
//
//    }
//

//
//    /**
//     * Record Model files
//     */
//    protected  function  recordModels()
//    {
//        //record main model (ONLY ONCE)
//        if (!file_exists($this->path.'/'.$this->config_data['name'].'.php'))
//        {
//            @mkdir($this->path);
//            file_put_contents($this->path.'/'.$this->config_data['name'].'.php',
//                $this->app['view']->make('crud-wizard::stubs/crud_model_class', ['model'=>$this->config_data])->render()
//            );
//        }
//
//        //record base model
//        @mkdir($this->path.'/Crud');
//        file_put_contents($this->path.'/Crud/'.$this->config_data['name'].'Base.php',
//            $this->app['view']->make('crud-wizard::stubs/crud_base_model_class', ['model'=>$this->config_data])->render()
//        );
//
//
//    }//
//
//    /**
//     * Record migrations
//     */
//    protected function recordMigrations()
//    {
//
//        $this->recordPivotMigrations();
//        $this->recordAddFieldsMigrations();
//
//
//    }//
//
//    /**
//     *  Record mirations for add fields
//     */
//    private function recordAddFieldsMigrations()
//    {
//        if (count($this->add_fields))
//        {
//
//            $migrator = new Migrator();
//            $columns = [];
//
//            foreach ($this->add_fields as $fname=>$fdesc)
//            {
//                if (!empty($fdesc['type'])) {
//                    if ($control = Form::getControlByType($fdesc['type'])) {
//                        if ($control instanceof WizardableField) {
//                            $dbtype = $migrator->getColumDbTypeByEditType($fdesc['type']);
//                            if (!empty($dbtype)) {
//                                $columns[$fname] = $dbtype;
//                            }
//                        }
//                    }
//                }
//            }
//
//
//            if (count($columns)) {
//                if ($migrator->appendColumns($this->table, $columns)) {
//                    $this->migrations_created = true;
//                }
//            }
//
//        }
//    }//
//
//
//    /**
//     *  run artisan migrate
//     */
//    private function migrate()
//    {
//        if ($this->migrations_created)
//        {
//            $migrator = new Migrator();
//            if (!$migrator->migrate())
//            {
//                $this->error = $migrator->error;
//            }
//        }
//    }
//
//
//    private function recordPivotMigrations()
//    {
//        if (!empty($this->migrations_data['pivot']) && is_array($this->migrations_data['pivot']))
//        {
//            $migrator = new Migrator();
//            foreach ($this->migrations_data['pivot'] as $p)
//            {
//
//                $this->migrations_created = true;
//                $migrator->createPivotTable($p);
//
//            }
//        }
//
//    }


}