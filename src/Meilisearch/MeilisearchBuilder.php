<?php

namespace Codewiser\Scout\Meilisearch;

use Carbon\CarbonPeriod;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Conditionable;

class MeilisearchBuilder
{
    use Conditionable;

    /**
     * @var array<MeilisearchExpression>
     */
    public array $query = [];

    public function build(): string
    {
        $str = '';

        foreach ($this->query as $query) {
            $str .= ' '.$query->toString(!$str);
        }

        return trim($str);
    }

    /**
     * Add a basic where clause to the query.
     */
    public function where(
        string|\Closure $column,
        $operator = null,
        $value = null,
        $boolean = 'AND',
        $not = false
    ): static {
        if ($column instanceof \Closure) {
            $nested = new static;
            call_user_func($column, $nested);
            $this->query[] =
                new MeilisearchExpression(
                    value: '('.$nested->build().')',
                    boolean: $boolean,
                    not: $not
                );
        } else {
            if (is_null($value)) {
                $value = $operator;
                $operator = '=';
            }
            if (is_null($operator)) {
                $operator = '=';
            }

            $this->query[] = new MeilisearchExpression(
                column: $column,
                operator: $operator,
                value: $value,
                boolean: $boolean,
                not: $not
            );
        }

        return $this;
    }

    /**
     * Add a basic "OR" clause to the query.
     */
    public function orWhere(string|\Closure $column, $value = null): static
    {
        return $this->where(
            column: $column,
            value: $value,
            boolean: 'OR'
        );
    }

    /**
     * Add a basic "NOT" clause to the query.
     */
    public function whereNot(string|\Closure $column, $value = null, $boolean = 'AND'): static
    {
        return $this->where(
            column: $column,
            value: $value,
            boolean: $boolean,
            not: true
        );
    }

    /**
     * Add a basic "OR NOT" clause to the query.
     */
    public function orWhereNot(string|\Closure $column, $value = null): static
    {
        return $this->whereNot(
            column: $column,
            value: $value,
            boolean: 'OR'
        );
    }

    /**
     * Add an "IN" clause to the query.
     */
    public function whereIn(string $column, $values, $boolean = 'AND', $not = false): static
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        $values = Arr::wrap($values);

        if (count($values) !== count(Arr::flatten($values, 1))) {
            throw new \InvalidArgumentException('Nested arrays may not be passed to whereIn method.');
        }

        $values = Arr::map($values, fn($value) => $value instanceof \DateTimeInterface ? $value->getTimestamp() : $value);

        return $this->where(
            column: $column,
            operator: 'IN',
            value: '['.implode(', ', $values).']',
            boolean: $boolean,
            not: $not
        );
    }

    /**
     * Add an "OR IN" clause to the query.
     */
    public function orWhereIn(string $column, $values): static
    {
        return $this->whereIn(
            column: $column,
            values: $values,
            boolean: 'OR'
        );
    }

    /**
     * Add a "NOT IN" clause to the query.
     */
    public function whereNotIn(string $column, $values): static
    {
        return $this->whereIn(
            column: $column,
            values: $values,
            not: true
        );
    }

    /**
     * Add an "OR NOT IN" clause to the query.
     */
    public function orWhereNotIn(string $column, $values): static
    {
        return $this->whereIn(
            column: $column,
            values: $values,
            boolean: 'OR',
            not: true
        );
    }

    /**
     * Add a between statement to the query.
     */
    public function whereBetween(string $column, iterable $values, string $boolean = 'AND', bool $not = false): static
    {
        if ($values instanceof CarbonPeriod) {
            $values = [
                $values->getStartDate(),
                $values->getEndDate(),
            ];
        }

        $values = Arr::map($values, fn($value) => $value instanceof \DateTimeInterface ? $value->getTimestamp() : $value);

        $bottom = $values[0] ?? null;
        $top = $values[1] ?? null;

        if ($bottom && $top) {
            $this->query[] = new MeilisearchExpression(
                column: $column,
                value: $bottom.' TO '.$top,
                boolean: $boolean,
                not: $not
            );
        } elseif ($bottom) {
            $this->query[] = new MeilisearchExpression(
                column: $column,
                operator: '>=',
                value: $bottom,
                boolean: $boolean,
                not: $not
            );
        } elseif ($top) {
            $this->query[] = new MeilisearchExpression(
                column: $column,
                operator: '<=',
                value: $top,
                boolean: $boolean,
                not: $not
            );
        }

        return $this;
    }

    public function orWhereBetween(string $column, iterable $values): static
    {
        return $this->whereBetween(
            column: $column,
            values: $values,
            boolean: 'OR'
        );
    }

    /**
     * Add a "EXISTS" clause to the query.
     */
    public function whereExists(string $column, string $boolean = 'AND', bool $not = false): static
    {
        $this->query[] = new MeilisearchExpression(
            column: $column,
            operator: 'EXISTS',
            boolean: $boolean,
            not: $not
        );

        return $this;
    }

    /**
     * Add a "OR EXISTS" clause to the query.
     */
    public function orWhereExists(string $column): static
    {
        return $this->whereExists(
            column: $column,
            boolean: 'OR'
        );
    }

    /**
     * Add a "NOT EXISTS" clause to the query.
     */
    public function whereNotExists(string $column, string $boolean = 'AND'): static
    {
        return $this->whereExists(
            column: $column,
            boolean: $boolean,
            not: true
        );
    }

    /**
     * Add a "OR NOT EXISTS" clause to the query.
     */
    public function orWhereNotExists(string $column): static
    {
        return $this->whereNotExists(
            column: $column,
            boolean: 'OR'
        );
    }

    /**
     * Add a "IS EMPTY" clause to the query.
     */
    public function whereEmpty(string|array $columns, string $boolean = 'AND', bool $not = false): static
    {
        foreach (Arr::wrap($columns) as $column) {
            $this->query[] = new MeilisearchExpression(
                column: $column,
                operator: 'IS EMPTY',
                boolean: $boolean,
                not: $not
            );
        }

        return $this;
    }

    /**
     * Add an "OR IS EMPTY" clause to the query.
     */
    public function orWhereEmpty(string|array $columns): static
    {
        return $this->whereEmpty(
            columns: $columns,
            boolean: 'OR'
        );
    }

    /**
     * Add a "NOT IS EMPTY" clause to the query.
     */
    public function whereNotEmpty(string|array $columns, string $boolean = 'AND'): static
    {
        return $this->whereEmpty(
            columns: $columns,
            boolean: $boolean,
            not: true,
        );
    }

    /**
     * Add an "OR NOT IS EMPTY" clause to the query.
     */
    public function orWhereNotEmpty(string|array $columns): static
    {
        return $this->whereNotEmpty(
            columns: $columns,
            boolean: 'OR',
        );
    }

    /**
     * Add a "IS NULL" clause to the query.
     */
    public function whereNull(string|array $columns, string $boolean = 'AND', bool $not = false): static
    {
        foreach (Arr::wrap($columns) as $column) {
            $this->query[] = new MeilisearchExpression(
                column: $column,
                operator: 'IS NULL',
                boolean: $boolean,
                not: $not
            );
        }

        return $this;
    }

    /**
     * Add an "OR IS NULL" clause to the query.
     */
    public function orWhereNull(string|array $columns): static
    {
        return $this->whereNull(
            columns: $columns,
            boolean: 'OR'
        );
    }

    /**
     * Add a "NOT IS NULL" clause to the query.
     */
    public function whereNotNull(string|array $columns, $boolean = 'and'): static
    {
        return $this->whereNull(
            columns: $columns,
            boolean: $boolean,
            not: true,
        );
    }

    /**
     * Add an "OR NOT IS NULL" clause to the query.
     */
    public function orWhereNotNull(string|array $columns): static
    {
        return $this->whereNotNull(
            columns: $columns,
            boolean: 'OR'
        );
    }
}