<?php

namespace Cann\Admin\OAuth;

use Illuminate\Support\ServiceProvider;

class AdminOAuthServiceProvider extends ServiceProvider
{
    public function boot(AdminOAuth $extension)
    {
        if (! AdminOAuth::boot()) {
            return;
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'admin-oauth');
        }

        if ($this->app->runningInConsole()) {

            $this->publishes([__DIR__.'/../config' => config_path()], 'admin-oauth');
            $this->publishes([__DIR__.'/../public' => public_path()], 'admin-oauth');
            $this->publishes([__DIR__.'/../resources/views' => resource_path('views')], 'admin-oauth');

            $this->commands([
                Console\Commands\InstallCommand::class,
                Console\Commands\SyncBindRelationFromPassport::class,
            ]);
        }

        $this->app->booted(function () {
            // 登录后才可访问的路由
            AdminOAuth::routes(__DIR__ . '/../routes/admin.php');
        });

        $this->configAdmin();
    }

    private function configAdmin()
    {
        $exceptRoutes = array_merge(config('admin.auth.excepts'), [
            'oauth/authorize',
            'oauth/callback',
            'oauth/bind-account',
        ]);

        config(['admin.auth.excepts' => $exceptRoutes]);

        config(['admin.skin' => 'skin-green']);

        config(['admin.layout' => ['sidebar-mini']]);
    }
}
