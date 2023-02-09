<?php

namespace Techenby\TransistorForStatamic\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Collection;
use Statamic\Facades\Site;
use Statamic\Facades\Fieldset as FieldsetFacade;
use Statamic\Fields\Fieldset;

class InstallAddon extends Command
{
    use RunsInPlease;

    protected $signature = 'tfs:install';

    protected $description = 'Create collections for shows and episodes.';

    public function handle()
    {
        $this->line('Creating shows collection...');
        $this->createShowsCollection();

        $this->line('Creating episodes collection...');
        $this->createEpisodesCollection();
    }

    private function createShowsCollection()
    {
        $showSettings = config('statamic.transistor.show_collection');

        if (Collection::find($showSettings['handle'])) {
            $this->line('The shows collection already exists, skipping...');
            return;
        }

        $collection = Collection::make($showSettings['handle']);

        $collection->title($showSettings['title'])
            ->pastDateBehavior('public')
            ->futureDateBehavior('private');

        if (Site::hasMultiple()) {
            $collection->sites([Site::default()->handle()]);
        }

        $collection->save();
    }

    private function createEpisodesCollection()
    {
        $episodeSettings = config('statamic.transistor.episode_collection');

        if (Collection::find($episodeSettings['handle'])) {
            $this->line('The episodes collection already exists, skipping...');

            return;
        }

        $collection = Collection::make($episodeSettings['handle']);

        $collection->title($episodeSettings['title'])
            ->pastDateBehavior('public')
            ->futureDateBehavior('private');

        if (Site::hasMultiple()) {
            $collection->sites([Site::default()->handle()]);
        }

        $collection->save();
    }
}
