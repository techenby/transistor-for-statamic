<?php

namespace Techenby\TransistorForStatamic\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Statamic\Console\RunsInPlease;

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
    protected $description = 'Generate an SEO report.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client([
            'base_uri' => 'https://api.transistor.fm',
            'headers' => ['x-api-key' => config('transistor-for-statamic.api_key')],
        ]);
        $this->line('Importing shows...');

        $response = $client->get('v1/shows')->getBody()->getContents();

        dd($response);




        $this->line('Importing episodes...');
    }
}
