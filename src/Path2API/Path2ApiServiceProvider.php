<?php

namespace Pomek\Path2API;

use Illuminate\Filesystem\Filesystem;

class Path2ApiServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $configPath = __DIR__ . '/../config/path2api.php';
        $this->publishes([$configPath => config_path('path2api.php')], 'config');
        $this->mergeConfigFrom($configPath, 'path2api');
    }

    public function register()
    {
        $this->registerCommands();
    }

    protected function registerCommands()
    {
        $this->app->bindIf('command.path2api', function () {
            return new GenerateDocsConsole($this->app['router'], $this->app['config'], new Filesystem);
        });

        $this->commands([
            'command.path2api',
        ]);
    }

}
