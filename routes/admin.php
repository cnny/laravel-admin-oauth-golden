<?php

use Cann\Admin\OAuth\Controllers\UserController;
use Cann\Admin\OAuth\Controllers\ThirdAccountController;

Route::resource('auth/users', UserController::class)->names('admin.auth.users');

$authController = ThirdAccountController::class;

Route::get('auth/login', $authController . '@getLogin')->name('admin.login');
Route::get('auth/logout', $authController . '@getLogout')->name('admin.logout');
Route::get('oauth/authorize', $authController . '@goToAuthorize');
Route::get('oauth/callback', $authController . '@oauthCallback');
Route::get('oauth/bind-account', $authController . '@bindAccount');
Route::post('oauth/bind-account', $authController . '@bindAccount');
