<?php

namespace Cann\Admin\OAuth;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'oauth');

        $this->registerPublishing();
    }

    public function register()
    {
        // do nothing
    }

    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()], 'laravel-admin-oauth');
            $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], 'laravel-admin-oauth');
            $this->publishes([__DIR__ . '/../resources/assets' => public_path('vendor/laravel-admin-oauth')], 'laravel-admin-oauth');
        }
    }

    public static function extend(string $class, string $source, string $sourceName = '')
    {
        $sourceName = $sourceName ?: $source;

        config([
            'admin-oauth.sources.' . $source =>  compact('source', 'sourceName', 'class'),
        ]);
    }
}
