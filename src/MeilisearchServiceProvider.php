<?php

namespace Codewiser\Meilisearch;

use Codewiser\Meilisearch\Attributes\MeilisearchFilterableAttributes;
use Codewiser\Meilisearch\Attributes\MeilisearchSortableAttributes;
use Codewiser\Meilisearch\Console\Commands\MeilisearchRebuildCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class MeilisearchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('scout.driver') === 'meilisearch') {
            $this->setupMeilisearchIndexSettings();
            $this->registerCommands();
        }
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {

            $commands = [
                MeilisearchRebuildCommand::class,
            ];

            $this->commands($commands);
        }
    }

    protected function setupMeilisearchIndexSettings(): void
    {
        $config = [];
        $searchable = config('scout.meilisearch.searchable', Meilisearch::searchables());

        foreach ($searchable as $model) {
            $config[$model] = [
                'filterableAttributes' => Meilisearch::filterables($model),
                'sortableAttributes'   => Meilisearch::sortables($model),
            ];
        }

        config()->set('scout.meilisearch.index-settings', $config);
    }
}