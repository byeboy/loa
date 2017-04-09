<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});
$app->get('/models', 'ModelController@index');
$app->get('/files/{type}/{id}', 'FileController@relation');
$app->get('/notices', 'NoticeController@index');
$app->get('/tasks', 'TaskController@index');
$app->get('/zip/tasks/{id}', 'TaskController@getAllFiles');
$app->post('/tasks', 'TaskController@create');
$app->post('/records/models/{id}', 'ModelController@recorder');


$app->group(['prefix' => 'api'], function () use ($app) {
    $app->get('/', function ()    {
        // Uses Auth Middleware
    });
    $app->post('reg', 'UserController@reg');
    $app->post('login', 'UserController@login');
    $app->post('files', 'FileController@create');
    $app->get('auth', 'UserController@auth');

    $app->get('test', 'BranchController@test');
    $app->post('test', 'BranchController@testPost');

    $app->group(['middleware' => 'auth'], function () use ($app) {
        /**
         * Routes for User
         *
         * REST
         */
        $app->get('users', 'UserController@index');
        $app->get('users/{id}', 'UserController@get');
        $app->get('users/{param}/{val}', 'UserController@search');
        $app->post('users', 'UserController@create');
        $app->put('users/{id}', 'UserController@update');
        $app->delete('users/{id}', 'UserController@delete');

        /**
         * Routes for Branch
         *
         * REST
         */
        $app->get('branches', 'BranchController@index');
        $app->get('branches/{id}', 'BranchController@get');
        $app->get('branches/{param}/{val}', 'BranchController@search');
        $app->post('branches', 'BranchController@create');
        $app->put('branches/{id}', 'BranchController@update');
        $app->delete('branches/{id}', 'BranchController@delete');

        /**
         * Routes for Notice
         *
         * REST
         */
        $app->get('notices', 'NoticeController@index');
        $app->get('notices/{id}', 'NoticeController@get');
        $app->get('notices/{param}/{val}', 'NoticeController@search');
        $app->post('notices', 'NoticeController@create');
        $app->put('notices/{id}', 'NoticeController@update');
        $app->delete('notices/{id}', 'NoticeController@delete');

        /**
         * Routes for Task
         *
         * REST
         */
        $app->get('tasks', 'TaskController@index');
        $app->get('tasks/user[/{id}]', 'TaskController@all');
        $app->get('tasks/{id}', 'TaskController@get');
        $app->get('tasks/{param}/{val}', 'TaskController@search');
        $app->post('tasks', 'TaskController@create');
        $app->put('tasks/{id}', 'TaskController@update');
        $app->patch('tasks/{id}', 'TaskController@patch');
        $app->patch('tasks/{id}/progress', 'TaskController@progressPatch');
        $app->patch('steps/{id}', 'TaskController@stepPatch');
        $app->delete('tasks/{id}', 'TaskController@delete');
        $app->get('zip/tasks/{id}', 'TaskController@getAllFiles');

        /**
         * Routes for PartProperty
         *
         * REST
         */
        $app->get('part-properties', 'PartPropertyController@index');
        $app->get('cabinets/{id}', 'CabinetController@get');
        $app->get('cabinets/{param}/{val}', 'CabinetController@search');
        $app->post('cabinets', 'CabinetController@create');
        $app->put('cabinets/{id}', 'CabinetController@update');
        $app->delete('cabinets/{id}', 'CabinetController@delete');

        /**
         * Routes for Material
         *
         * REST
         */
        /*$app->get('materials', 'MaterialController@index');
        $app->get('materials/{id}', 'MaterialController@get');
        $app->get('materials/{param}/{val}', 'MaterialController@search');
        $app->post('materials', 'MaterialController@create');
        $app->put('materials/{id}', 'MaterialController@update');
        $app->patch('materials/{id}', 'MaterialController@patch');
        $app->delete('materials/{id}', 'MaterialController@delete');*/

        /**
         * Routes for Model
         *
         * REST
         */
        $app->get('models', 'ModelController@index');
        $app->get('models/{id}', 'ModelController@get');
        $app->get('models/{param}/{val}', 'ModelController@search');
        $app->post('models/{upId}', 'ModelController@createRelation');
        $app->patch('models/relations/{upId}', 'ModelController@patchRelation');
        $app->delete('models/{id}/{type}/{rid}', 'ModelController@delRelation');
        $app->post('models', 'ModelController@create');
        $app->put('models/{id}', 'ModelController@update');
        $app->patch('models/{id}', 'ModelController@patch');
        $app->delete('models/{id}', 'ModelController@delete');

        /**
         * Routes for Cabinet
         *
         * REST
         */
        $app->get('cabinets', 'CabinetController@index');
        $app->get('cabinets/{id}', 'CabinetController@get');
        $app->get('cabinets/{param}/{val}', 'CabinetController@search');
        $app->post('cabinets/{upId}', 'CabinetController@createRelation');
        $app->patch('cabinets/relations/{upId}', 'CabinetController@patchRelation');
        $app->delete('cabinets/{id}/{type}/{rid}', 'CabinetController@delRelation');
        $app->get('cabinets/relation/{type}/{id}', 'CabinetController@relation');
        $app->post('cabinets', 'CabinetController@create');
        $app->put('cabinets/{id}', 'CabinetController@update');
        $app->patch('cabinets/{id}', 'CabinetController@patch');
        $app->delete('cabinets/{id}', 'CabinetController@delete');

        /**
         * Routes for Fan
         *
         * REST
         */
        $app->get('fans', 'FanController@index');
        $app->get('fans/{id}', 'FanController@get');
        $app->get('fans/{param}/{val}', 'FanController@search');
        $app->post('fans/{upId}', 'FanController@createRelation');
        $app->patch('fans/relations/{upId}', 'FanController@patchRelation');
        $app->delete('fans/{id}/{type}/{rid}', 'FanController@delRelation');
        $app->get('fans/relation/{type}/{id}', 'FanController@relation');
        $app->post('fans', 'FanController@create');
        $app->put('fans/{id}', 'FanController@update');
        $app->patch('fans/{id}', 'FanController@patch');
        $app->delete('fans/{id}', 'FanController@delete');

        /**
         * Routes for Part
         *
         * REST
         */
        $app->get('parts', 'PartController@index');
        $app->get('parts/{id}', 'PartController@get');
        $app->get('parts/{param}/{val}', 'PartController@search');
        $app->post('parts/{upId}', 'PartController@createRelation');
        $app->patch('parts/relations/{upId}', 'PartController@patchRelation');
        $app->delete('parts/{id}/{type}/{rid}', 'PartController@delRelation');
        $app->get('parts/relation/{type}/{id}', 'PartController@relation');
        $app->post('parts', 'PartController@create');
        $app->put('parts/{id}', 'PartController@update');
        $app->patch('parts/{id}', 'PartController@patch');
        $app->delete('parts/{id}', 'PartController@delete');

        $app->get('files', 'FileController@index');
        $app->get('files/relation/{type}/{id}', 'FileController@relation');

        $app->group(['prefix' => 'records'], function () use ($app) {
            $app->get('models', 'ModelController@record');
            $app->post('models/{id}', 'ModelController@recorder');
        });
    });

//    $app->group(['prefix' => 'branch'], function () use ($app) {
//        $app->get('all', 'BranchController@index');
//        $app->post('create', 'BranchController@create');
//    });
});