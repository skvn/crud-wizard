<?php
$adm_route_params = [
    'prefix'=>'admin',
    'namespace' => 'Skvn\CrudWizard\Controllers',
    'middleware' =>  explode(',',env('APP_BACKEND_MIDDLEWARE',"web,auth"))
];

$domain = env('APP_BACKEND_DOMAIN');

if ($domain)
{
    $adm_route_params['domain'] = $domain;
}

Route::group($adm_route_params, function() {


    Route::post('crud_setup/migration/create',                      array('as'=>'wizard_migrate_create',        'uses' => 'WizardController@migrationCreate'));
    Route::any('crud_setup/wizard/{method}',                        array(                                      'uses' => 'WizardController@getWizardMethod'));

    //Route::any('crud_setup/menu',                                   array('as' => 'wizard_menu',                'uses' => 'WizardController@menu'));
    Route::any('crud_setup/{table}',                                array('as' => 'wizard_model',               'uses' => 'WizardController@model'));
    Route::any('crud_setup',                                        array('as' => 'wizard_index',               'uses' => 'WizardController@index'));


});


