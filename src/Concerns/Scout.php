<?php

namespace Codewiser\Scout\Concerns;

use Algolia\AlgoliaSearch\SearchIndex as Algolia;
use Codewiser\Scout\Meilisearch\MeilisearchBuilder;
use Illuminate\Database\Eloquent\Builder as Database;
use Meilisearch\Endpoints\Indexes as Meilisearch;

abstract class Scout
{
    protected array $debug = [];

    /**
     * Use another Scout driver.
     */
    public function use(string $driver): static
    {
        config()->set('scout.driver', $driver);

        return $this;
    }

    public static function when(string $driver, array $attributes = []): array
    {
        return config('scout.driver') == $driver ? $attributes : [];
    }

    public function debug(): array
    {
        return $this->debug;
    }

    /**
     * Scout callback (custom search)
     *
     * @see https://laravel.com/docs/11.x/scout#customizing-engine-searches
     */
    public function __invoke(): mixed
    {
        $args = func_get_args();

        return match (config('scout.driver')) {
            'database'    => $this->_database(...$args),
            'meilisearch' => $this->_meilisearch(...$args),
            'algolia'     => $this->_algolia(...$args),
            default       => null
        };
    }

    private function _database(Database $builder)
    {
        if ($this instanceof ScoutsDatabase) {
            $builder = $this->database($builder);
            $this->debug['database'] = $builder->toRawSql();
            return $builder;
        } else {
            $this->debug['database'] = get_class($this).' doesnt implement '.ScoutsDatabase::class;
            return null;
        }
    }

    private function _meilisearch(Meilisearch $builder, ?string $query, array $options)
    {
        if ($this instanceof ScoutsMeilisearch) {
            $filter = $this->meilisearch(new MeilisearchBuilder)->build();
            if ($filter) {
                $options['filter'] = isset($options['filter']) ? $options['filter'].' AND '.$filter : $filter;
            }
            $this->debug['meilisearch'] = [
                'query'   => $query,
                'options' => $options
            ];
            return $builder->search($query, $options);
        } else {
            $this->debug['meilisearch'] = get_class($this).' doesnt implement '.ScoutsMeilisearch::class;
            return null;
        }
    }

    private function _algolia(Algolia $algolia, string $query, array $options)
    {
        if ($this instanceof ScoutsAlgolia) {
            $options = $this->algolia($options);
            $this->debug['algolia'] = [
                'query'   => $query,
                'options' => $options
            ];
            return $algolia->search($query, $options);
        } else {
            $this->debug['algolia'] = get_class($this).' doesnt implement '.ScoutsAlgolia::class;
            return null;
        }
    }
}