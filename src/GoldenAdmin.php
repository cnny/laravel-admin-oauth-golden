<?php

namespace Cann\Admin\OAuth;

use Encore\Admin\Admin;
use Cann\Admin\OAuth\Controllers\AuthController;
use Cann\Admin\OAuth\Controllers\UserController;

class GoldenAdmin extends Admin
{
    /**
     * Register the laravel-admin builtin routes.
     *
     * @return void
     */
    public function routes()
    {
        parent::routes();

        static::initRouteConfig();

        $attributes = [
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ];

        app('router')->group($attributes, function ($router) {

            $router->namespace('\Cann\Admin\OAuth\Controllers')->group(function ($router) {
                $router->resource('auth/users', 'UserController')->names('admin.auth.users');
            });

            $authController = config('admin-oauth.controller', AuthController::class);

            $router->get('auth/login', $authController . '@getLogin')->name('admin.login');
            $router->post('auth/login', $authController . '@postLogin');
            $router->get('auth/logout', $authController . '@getLogout')->name('admin.logout');
            $router->get('auth/setting', $authController . '@getSetting')->name('admin.setting');
            $router->put('auth/setting', $authController . '@putSetting');
            $router->get('/oauth/authorize', $authController . '@toAuthorize');
            $router->get('/oauth/callback', $authController . '@oauthCallback');
            $router->get('/oauth/bind-account', $authController . '@bindAccount');
            $router->post('/oauth/bind-account', $authController . '@bindAccount');
        });
    }

    private static function initRouteConfig()
    {
        $exceptRoutes = array_merge(config('admin.auth.excepts'), [
            'oauth/authorize',
            'oauth/callback',
            'oauth/bind-account',
        ]);

        config([
            'admin.auth.excepts' => $exceptRoutes,
        ]);
    }
}
