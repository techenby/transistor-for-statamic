<?php

namespace Techenby\TransistorForStatamic\Commands;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;

class ImportShowsAndEpisodes extends Command
{
    use RunsInPlease;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tfs:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import shows and episodes from Transistor.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client([
            'base_uri' => 'https://api.transistor.fm',
            'headers' => ['x-api-key' => config('statamic.transistor.api_key')],
        ]);
        $this->line('Importing shows...');

        $response = $client->get('v1/shows');

        $shows = json_decode($response->getBody()->getContents(), true)['data'];

        foreach($shows as $show) {
            $found = $this->findEntry('show', 'transistor_id', $show['id']);

            if ($found === null) {
                $this->createShow($show);
            }
        }

        $this->line('Importing episodes...');

        $response = $client->get('v1/episodes?fields[episode][]=title');

        $data = json_decode($response->getBody()->getContents(), true);
        dd($data);

        $this->processEpisodes($data['data']);

        if ($data['meta']['totalPages'] > 1) {
            foreach (range(2, $data['meta']['totalPages']) as $page) {
                $response = $client->get("v1/episodes?fields[episode][]=title&pagination[page]={$page}");

                $data = json_decode($response->getBody()->getContents(), true);
                dd($data);

                $this->processEpisodes($data['data']);
            }
        }

    }

    private function createShow($show)
    {
        $attributes = $show['attributes'];
        $attributes['transistor_id'] = $show['id'];
        $attributes['transistor_title'] = $attributes['title'];
        $attributes['transistor_slug'] = $attributes['slug'];

        $entry = Entry::make()
            ->collection(config('statamic.transistor.collections.show'))
            ->data($attributes)
            ->slug($attributes['slug'])
            ->date(Carbon::parse($attributes['created_at'])->format('Y-m-d'))
            ->publish();

        $entry->save();
    }

    private function createEpisode($episode)
    {
        $attributes = $episode['attributes'];
        $attributes['[transistor_]id'] = $episode['id'];
        $attributes['transistor_title'] = $attributes['title'];
        $attributes['transistor_show_id'] = $episode['relationships']['show']['data']['id'];
        $attributes['podcast_show'] = $this->findEntry('show', 'transistor_id', $attributes['transistor_show_id'])->id;

        $slug = Str::slug($attributes['title']);

        $entry = Entry::make()
            ->collection(config('statamic.transistor.collections.episode'))
            ->data($attributes)
            ->slug($slug)
            ->date(Carbon::parse($attributes['created_at'])->format('Y-m-d'))
            ->publish();

        $entry->save();
    }

    private function findEntry($type, $column, $value)
    {
        $collection = config("statamic.transistor.collections.{$type}");
        return Entry::query()
                ->where('collection', $collection)
                ->where($column, $value)
                ->first();
    }

    private function processEpisodes($episodes)
    {
        foreach($episodes as $episode) {
            $found = $this->findEntry('episode', 'transistor_id', $episode['id']);

            if ($found === null) {
                $this->createEpisode($episode);
            }
        }
    }
}
