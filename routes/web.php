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

$app->group(['prefix' => 'api'], function () use ($app) {
    $app->get('/', function ()    {
        // Uses Auth Middleware
    });
    $app->get('test', 'BranchController@test');
    $app->post('test', 'BranchController@testPost');

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
    $app->post('reg', 'UserController@reg');
    $app->post('login', 'UserController@login');

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
    $app->delete('tasks/{id}', 'TaskController@delete');

//    $app->group(['prefix' => 'branch'], function () use ($app) {
//        $app->get('all', 'BranchController@index');
//        $app->post('create', 'BranchController@create');
//    });
});