<?php

namespace Codewiser\Scout\Concerns;

interface ScoutsAlgolia
{
    /**
     * Customize search options for Algolia.
     */
    public function algolia(array $options): array;
}