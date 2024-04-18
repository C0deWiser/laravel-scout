<?php

namespace Codewiser\Scout\Attributes;

use Attribute;
use Illuminate\Support\Arr;

#[Attribute]
class MeilisearchFilterableAttributes
{
    /**
     * The prefix search columns.
     *
     * @var array
     */
    public array $columns = [];

    /**
     * Create a new attribute instance.
     *
     * @param array|string $columns
     * @return void
     */
    public function __construct(array|string $columns)
    {
        $this->columns = Arr::wrap($columns);
    }
}
