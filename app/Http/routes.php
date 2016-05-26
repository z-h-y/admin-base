<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(array('prefix' => 'admin'), function() {

    // 无需登录就能访问的API
    Route::group(array('prefix' => 'api', 'namespace' => 'Api'), function () {
        // 用户登录相关
        // session routes for user login/logout
        // show -> check user login or not
        // store -> user login
        // destroy -> user logout
        Route::resource('sessions', 'SessionController', array('only' => array('show', 'destroy', 'store')));
    });

    // 只需要用户登录即可请求的API
    Route::group(array('prefix' => 'api', 'middleware' => 'auth.api', 'namespace' => 'Api'), function () {
        Route::group(array('prefix' => 'admin', 'namespace' => 'Admin'), function () {
            // 角色与权限
            Route::resource('users', 'UserController', array('only' => array('index')));
            Route::resource('roles', 'RoleController', array('only' => array('index')));
            Route::resource('permissions', 'PermissionController', array('only' => array('index')));

            Route::get('users/{id}/roles', array('uses' => 'UserController@getRoles'));
            Route::get('users/{id}/permissions', array('uses' => 'UserController@getPermissions'));
            Route::get('roles/{id}/permissions', array('uses' => 'RoleController@getPermissions'));
            Route::get('roles/{id}/users', array('uses' => 'RoleController@getUsers'));

            // Codecs (字典)
            Route::resource('codecs', 'CodecsController', array('only' => array('index', 'show')));
        });

        // 更新登录用户个人信息
        Route::post('updateUserProfile', array('uses' => 'Admin\UserController@updateUserProfile'));

        // LeanCloud 配置信息
        Route::get('getLeanCloudConfig', array('uses' => 'LeanCloudController@search'));

        /****** Your app's routes - START ******/



        /****** Your app's routes - END ******/
    });

    // 需要登录且角色为ADMIN才能请求的API
    Route::group(array('prefix' => 'api', 'middleware' => 'auth.api', 'namespace' => 'Api'), function () {
        Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin', 'namespace' => 'Admin'), function () {
            // 角色与权限
            Route::resource('users', 'UserController', array('except' => array('index', 'create', 'edit')));
            Route::resource('roles', 'RoleController', array('except' => array('index', 'create', 'edit')));

            Route::put('users/{id}/roles', array('uses' => 'UserController@updateRoles'));
            Route::put('roles/{id}/permissions', array('uses' => 'RoleController@updatePermissions'));
        });
    });

    // 需要登录且角色为OWNER才能请求的API
    Route::group(array('prefix' => 'api', 'middleware' => 'auth.api', 'namespace' => 'Api'), function () {
        Route::group(array('prefix' => 'admin', 'middleware' => 'auth.owner', 'namespace' => 'Admin'), function () {
            // 权限
            Route::resource('permissions', 'PermissionController', array('except' => array('index', 'create', 'edit')));

            // Codecs (字典)
            Route::resource('codecs', 'CodecsController', array('only' => array('store', 'update', 'destroy')));
        });
    });

    // SSO相关API，无须登录
    Route::group(array('prefix' => 'sso'), function () {
        Route::get('login', array('uses' => 'SsoController@login'));
        Route::get('callback', array('uses' => 'SsoController@callback'));
    });

    Route::get('/', function () {
        $debug = Request::input('_debug');
        if ($debug) {
            return File::get(public_path() . '/app-debug.html');
        } else {
            return File::get(public_path() . '/app.html');
        }
    });

});

Route::get('/', function () {
    return 'Not found!';
});
