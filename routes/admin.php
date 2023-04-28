<?php

use Cann\Admin\OAuth\Controllers\UserController;
use Cann\Admin\OAuth\Controllers\RoleController;
use Cann\Admin\OAuth\Controllers\PermissionController;
use Cann\Admin\OAuth\Controllers\ThirdAccountController;

$userController       = config('admin-oauth.controllers.user', UserController::class);
$roleController       = config('admin-oauth.controllers.role', RoleController::class);
$permissionController = config('admin-oauth.controllers.permission', PermissionController::class);
$authController       = config('admin-oauth.controllers.auth', ThirdAccountController::class);

Route::resource('auth/users', $userController)->names('admin.auth.users');
Route::resource('auth/roles', $roleController)->names('admin.auth.roles');
Route::resource('auth/permissions', $permissionController)->names('admin.auth.permissions');
Route::get('auth/login', $authController . '@getLogin')->name('admin.login');
Route::get('auth/logout', $authController . '@getLogout')->name('admin.logout');
Route::get('oauth/authorize', $authController . '@goToAuthorize');
Route::get('oauth/callback', $authController . '@oauthCallback');
Route::get('oauth/bind-account', $authController . '@bindAccount');
Route::post('oauth/bind-account', $authController . '@bindAccount');
