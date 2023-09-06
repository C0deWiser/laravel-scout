<?php

namespace Codewiser\Meilisearch;

use Codewiser\Meilisearch\Attributes\MeilisearchFilterableAttributes;
use Codewiser\Meilisearch\Attributes\MeilisearchSortableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laravel\Scout\Searchable;

class Meilisearch
{
    static public function searchables(): array
    {
        $searchable = [];

        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, Model::class)) {
                foreach ((new \ReflectionClass($class))->getTraits() as $trait) {
                    if ($trait->getName() == Searchable::class) {
                        $searchable[] = $class;
                    }
                }
            }
        }

        return $searchable;
    }

    static public function filterables(string|Model $model): array
    {
        $filterable = [];

        foreach ((new \ReflectionMethod($model, 'toSearchableArray'))->getAttributes() as $attribute) {
            if ($attribute->getName() === MeilisearchFilterableAttributes::class) {
                $filterable = array_merge($filterable, Arr::wrap($attribute->getArguments()[0]));
            }
        }

        return $filterable;
    }

    static public function sortables(string|Model $model): array
    {
        $sortable = [];

        foreach ((new \ReflectionMethod($model, 'toSearchableArray'))->getAttributes() as $attribute) {
            if ($attribute->getName() === MeilisearchSortableAttributes::class) {
                $sortable = array_merge($sortable, Arr::wrap($attribute->getArguments()[0]));
            }
        }

        return $sortable;
    }
}