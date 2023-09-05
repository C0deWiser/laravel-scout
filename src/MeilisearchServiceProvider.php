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

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {

            $commands = [
                MeilisearchRebuildCommand::class,
            ];

            $this->commands($commands);
        }
    }

    protected function setupMeilisearchIndexSettings()
    {
        $config = [];
        $searchable = config('scout.meilisearch.searchable', []);

        foreach ($searchable as $model) {
            $config[$model] = $this->buildMeilisearchIndexSettings($model);
        }

        config()->set('scout.meilisearch.index-settings', $config);
    }

    protected function buildMeilisearchIndexSettings(string $model): array
    {
        $filterableAttributes = [];
        $sortableAttributes = [];

        foreach ((new \ReflectionMethod($model, 'toSearchableArray'))->getAttributes() as $attribute) {
            if ($attribute->getName() === MeilisearchFilterableAttributes::class) {
                $filterableAttributes = array_merge($filterableAttributes, Arr::wrap($attribute->getArguments()[0]));
            }
            if ($attribute->getName() === MeilisearchSortableAttributes::class) {
                $sortableAttributes = array_merge($sortableAttributes, Arr::wrap($attribute->getArguments()[0]));
            }
        }

        return [
            'filterableAttributes' => $filterableAttributes,
            'sortableAttributes'   => $sortableAttributes,
        ];
    }
}