<?php

namespace Techenby\TransistorForStatamic;

use Illuminate\Support\Facades\Artisan;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;
use Techenby\TransistorForStatamic\Commands\ImportShowsAndEpisodes;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        ImportShowsAndEpisodes::class,
    ];

    public function bootAddon()
    {
        $this
            ->bootAddonConfig()
            ->bootAddonInstallCommand();
        //     // ->bootAddonViews()
        //     // ->bootAddonBladeDirective()
        //     // ->bootAddonPermissions()
        //     // ->bootAddonNav()
        //     // ->bootAddonSubscriber()
        //     // ->bootAddonGlidePresets()
        //     ->bootAddonCommands();
    }

    protected function bootAddonConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/transistor.php', 'statamic.transistor');

        $this->publishes([
            __DIR__ . '/../config/transistor.php' => config_path('statamic/transistor.php'),
        ], 'transistor-config');

        return $this;
    }

    protected function bootAddonInstallCommand()
    {
        $this->publishes([
            __DIR__ . '/../resources/blueprints' => resource_path('blueprints'),
        ], 'transistor-blueprints');

        $this->publishes([
            __DIR__ . '/../content/collections' => base_path('content/collections'),
        ], 'transistor-content');

        Statamic::afterInstalled(function ($command) {
            Artisan::call('vendor:publish --tag=transistor-content');
            Artisan::call('vendor:publish --tag=transistor-blueprints');
        });
    }
}
