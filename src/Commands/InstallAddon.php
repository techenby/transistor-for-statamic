<?php

namespace Techenby\TransistorForStatamic\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Collection;

class InstallAddon extends Command
{
    use RunsInPlease;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tfs:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create collections for shows and episodes.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Creating shows collection...');

        Collection::create(['name' => 'Podcast Shows']);

        $this->line('Creating episodes collection...');
    }
}
