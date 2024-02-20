<?php

namespace Codewiser\Meilisearch\Console\Commands;

use Codewiser\Meilisearch\Meilisearch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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
        // https://laravel.com/docs/10.x/scout#modifying-the-import-query
        // The makeAllSearchableUsing method may not be applicable when using a queue to batch import models.
        // Relationships are not restored when model collections are processed by jobs.
        config()->set('scout.queue', false);

        $searchable = config('scout.meilisearch.searchable', Meilisearch::searchables());

        foreach ($searchable as $class) {
            $this->call('scout:import', ['model' => $class]);
        }
    }
}
