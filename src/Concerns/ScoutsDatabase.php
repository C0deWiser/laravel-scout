<?php

namespace Codewiser\Scout\Concerns;


use Illuminate\Contracts\Database\Eloquent\Builder;

interface ScoutsDatabase
{
    /**
     * Build custom search for Database Scout.
     */
    function database(Builder $builder): Builder;
}