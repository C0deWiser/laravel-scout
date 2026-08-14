<?php

namespace Codewiser\Scout\Concerns;

use Illuminate\Contracts\Support\Arrayable;

interface ScoutBuilder extends \Stringable, Arrayable
{
    /**
     * Add a basic where clause to the query.
     */
    public function where(string|callable $column, $operator = null, $value = null): static;

    /**
     * Add a basic "OR" clause to the query.
     */
    public function orWhere(string|callable $column, $operator = null, $value = null): static;

    /**
     * Add a basic "NOT" clause to the query.
     */
    public function whereNot(string|callable $column, $value = null): static;

    /**
     * Add a basic "OR NOT" clause to the query.
     */
    public function orWhereNot(string|callable $column, $value = null): static;

    /**
     * Add an "IN" clause to the query.
     */
    public function whereIn(string $column, $values): static;

    /**
     * Add an "OR IN" clause to the query.
     */
    public function orWhereIn(string $column, $values): static;

    /**
     * Add a "NOT IN" clause to the query.
     */
    public function whereNotIn(string $column, $values): static;

    /**
     * Add an "OR NOT IN" clause to the query.
     */
    public function orWhereNotIn(string $column, $values): static;

    /**
     * Add a "BETWEEN" statement to the query.
     */
    public function whereBetween(string $column, iterable $values): static;

    /**
     * Add a "OR BETWEEN" statement to the query.
     */
    public function orWhereBetween(string $column, iterable $values): static;

    /**
     * Add a "EXISTS" clause to the query.
     */
    public function whereExists(string $column): static;

    /**
     * Add a "OR EXISTS" clause to the query.
     */
    public function orWhereExists(string $column): static;

    /**
     * Add a "NOT EXISTS" clause to the query.
     */
    public function whereNotExists(string $column): static;

    /**
     * Add a "OR NOT EXISTS" clause to the query.
     */
    public function orWhereNotExists(string $column): static;

    /**
     * Add an "IS EMPTY" clause to the query.
     */
    public function whereEmpty(string|array $columns): static;

    /**
     * Add an "OR IS EMPTY" clause to the query.
     */
    public function orWhereEmpty(string|array $columns): static;

    /**
     * Add a "NOT IS EMPTY" clause to the query.
     */
    public function whereNotEmpty(string|array $columns): static;

    /**
     * Add an "OR NOT IS EMPTY" clause to the query.
     */
    public function orWhereNotEmpty(string|array $columns): static;

    /**
     * Add a "IS NULL" clause to the query.
     */
    public function whereNull(string|array $columns): static;

    /**
     * Add an "OR IS NULL" clause to the query.
     */
    public function orWhereNull(string|array $columns): static;

    /**
     * Add a "NOT IS NULL" clause to the query.
     */
    public function whereNotNull(string|array $columns): static;

    /**
     * Add an "OR NOT IS NULL" clause to the query.
     */
    public function orWhereNotNull(string|array $columns): static;
}