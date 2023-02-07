<?php

namespace Techenby\TransistorForStatamic;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Permission;
use Statamic\Facades\User;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;
use Techenby\TransistorForStatamic\Commands\ImportShowsAndEpisodes;
use Techenby\TransistorForStatamic\Commands\InstallAddon;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        ImportShowsAndEpisodes::class,
        InstallAddon::class,
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
        Statamic::afterInstalled(function ($command) {
            $command->call('tfs:install');
        });
    }

    protected function bootAddonNav()
    {
        Nav::extend(function ($nav) {
            if ($this->userHasSeoPermissions()) {
                $nav->tools('SEO Pro')
                    ->route('seo-pro.index')
                    ->icon('seo-search-graph')
                    ->active('seo-pro')
                    ->children([
                        $nav->item(__('seo-pro::messages.reports'))->route('seo-pro.reports.index')->can('view seo reports'),
                        $nav->item(__('seo-pro::messages.site_defaults'))->route('seo-pro.site-defaults.edit')->can('edit seo site defaults'),
                        $nav->item(__('seo-pro::messages.section_defaults'))->route('seo-pro.section-defaults.index')->can('edit seo section defaults'),
                    ]);
            }
        });

        return $this;
    }

    protected function bootAddonCommands()
    {
        $this->commands([
            ImportShowsAndEpisodes::class,
        ]);
    }
}
