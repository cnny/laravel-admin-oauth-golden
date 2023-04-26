<?php

namespace Cann\Admin\OAuth\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'laravel-admin-golden:install';

    public function handle()
    {
        $this->replaceSomeFiles();
    }

    protected function replaceSomeFiles()
    {
        $this->replaceInFile(
            config_path('admin.php'),
            [
                'Encore\Admin\Auth\Database\Permission'    => 'Cann\Admin\OAuth\Models\Auth\Permission',
                'Encore\Admin\Auth\Database\Administrator' => 'Cann\Admin\OAuth\Models\Auth\Administrator',
            ]
        );
    }

    /**
     * Replace a given string in a given file.
     *
     * @param  string $path
     * @param  array  $replaces
     * @return void
     */
    protected function replaceInFile($path, array $replaces)
    {
        $old = file_get_contents($path);
        $new = str_replace(array_keys($replaces), array_values($replaces), $old);

        file_put_contents($path, $new);
    }
}
