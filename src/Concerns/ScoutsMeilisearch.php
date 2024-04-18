<?php

namespace Codewiser\Scout\Concerns;

use Codewiser\Scout\Meilisearch\MeilisearchBuilder;

interface ScoutsMeilisearch
{
    /**
     * Build custom filter for Meilisearch Scout.
     */
    function meilisearch(MeilisearchBuilder $builder): MeilisearchBuilder;
}