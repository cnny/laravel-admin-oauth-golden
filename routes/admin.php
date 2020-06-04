<?php

use Cann\Admin\OAuth\Controllers\UserController;
use Cann\Admin\OAuth\Controllers\RoleController;
use Cann\Admin\OAuth\Controllers\PermissionController;
use Cann\Admin\OAuth\Controllers\ThirdAccountController;

Route::resource('auth/users', UserController::class)->names('admin.auth.users');
Route::resource('auth/roles', RoleController::class)->names('admin.auth.roles');
Route::resource('auth/permissions', PermissionController::class)->names('admin.auth.permissions');

$authController = ThirdAccountController::class;

Route::get('auth/login', $authController . '@getLogin')->name('admin.login');
Route::get('auth/logout', $authController . '@getLogout')->name('admin.logout');
Route::get('oauth/authorize', $authController . '@goToAuthorize');
Route::get('oauth/callback', $authController . '@oauthCallback');
Route::get('oauth/bind-account', $authController . '@bindAccount');
Route::post('oauth/bind-account', $authController . '@bindAccount');
