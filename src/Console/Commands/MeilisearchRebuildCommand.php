<?php

namespace Codewiser\Scout\Console\Commands;

use Codewiser\Scout\Meilisearch\Meilisearch;
use Illuminate\Console\Command;

class MeilisearchRebuildCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:meilisearch-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-import all models (Meilisearch only)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $searchable = config('scout.meilisearch.searchable', Meilisearch::searchables());

        foreach ($searchable as $class) {
            $this->call('scout:import', ['model' => $class]);
        }
    }
}
