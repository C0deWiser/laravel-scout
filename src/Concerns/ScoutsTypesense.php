<?php

namespace Codewiser\Scout\Concerns;

interface ScoutsTypesense
{
    /**
     * Customize search options for Typesense.
     */
    public function typesense(array $options);
}