<?php

namespace Codewiser\Scout\Meilisearch;

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

    public function orWhere(string|\Closure $column, $value = null): static
    {
        return $this->where(
            column: $column,
            value: $value,
            boolean: 'OR'
        );
    }

    public function whereNot(string|\Closure $column, $value = null, $boolean = 'AND'): static
    {
        return $this->where(
            column: $column,
            value: $value,
            boolean: $boolean,
            not: true
        );
    }

    public function orWhereNot(string|\Closure $column, $value = null): static
    {
        return $this->whereNot(
            column: $column,
            value: $value,
            boolean: 'OR'
        );
    }

    public function whereIn(string $column, array $values, $boolean = 'AND', $not = false): static
    {
        return $this->where(
            column: $column,
            operator: 'IN',
            value: '['.implode(', ', $values).']',
            boolean: $boolean,
            not: $not
        );
    }

    public function orWhereIn(string $column, array $values): static
    {
        return $this->whereIn(
            column: $column,
            values: $values,
            boolean: 'OR'
        );
    }

    public function whereNotIn(string $column, array $values): static
    {
        return $this->whereIn(
            column: $column,
            values: $values,
            not: true
        );
    }

    public function orWhereNotIn(string $column, array $values): static
    {
        return $this->whereIn(
            column: $column,
            values: $values,
            boolean: 'OR',
            not: true
        );
    }

    public function whereBetween(string $column, $bottom = null, $top = null, $boolean = 'AND', $not = false): static
    {
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

    public function orWhereBetween(string $column, $bottom = null, $top = null): static
    {
        return $this->whereBetween(
            column: $column,
            bottom: $bottom,
            top: $top,
            boolean: 'OR'
        );
    }

    public function whereExists(string $column, $boolean = 'AND', $not = false): static
    {
        $this->query[] = new MeilisearchExpression(
            column: $column,
            operator: 'EXISTS',
            not: $not
        );

        return $this;
    }

    public function whereNotExists(string $column): static
    {
        return $this->whereExists(
            column: $column,
            not: true
        );
    }

    public function orWhereExists(string $column): static
    {
        return $this->whereExists(
            column: $column,
            boolean: 'OR'
        );
    }

    public function orWhereNotExists(string $column): static
    {
        return $this->whereExists(
            column: $column,
            boolean: 'OR',
            not: true
        );
    }

    public function whereIsEmpty($column, $boolean = 'AND', $not = false): static
    {
        $this->query[] = new MeilisearchExpression(
            column: $column,
            operator: 'IS EMPTY',
            boolean: $boolean,
            not: $not
        );

        return $this;
    }

    public function whereIsNotEmpty($column): static
    {
        return $this->whereIsEmpty(
            column: $column,
            not: true,
        );
    }

    public function orWhereIsEmpty($column): static
    {
        return $this->whereIsEmpty(
            column: $column,
            boolean: 'OR'
        );
    }

    public function orWhereIsNotEmpty($column): static
    {
        return $this->whereIsEmpty(
            column: $column,
            boolean: 'OR',
            not: true,
        );
    }

    public function whereIsNull($column, $boolean = 'AND', $not = false): static
    {
        $this->query[] = new MeilisearchExpression(
            column: $column,
            operator: 'IS NULL',
            boolean: $boolean,
            not: $not
        );

        return $this;
    }

    public function whereIsNotNull($column): static
    {
        return $this->whereIsNull(
            column: $column,
            not: true,
        );
    }

    public function orWhereIsNull($column): static
    {
        return $this->whereIsNull(
            column: $column,
            boolean: 'OR'
        );
    }

    public function orWhereIsNotNull($column): static
    {
        return $this->whereIsNull(
            column: $column,
            boolean: 'OR',
            not: true,
        );
    }
}