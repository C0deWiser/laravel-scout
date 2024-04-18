<?php

namespace Codewiser\Scout\Meilisearch;

class MeilisearchExpression
{
    public function __construct(
        protected ?string $column = null,
        protected ?string $operator = null,
        protected ?string $value = null,
        protected string $boolean = 'AND',
        protected bool $not = false,
    ) {
    }

    public function column(): string
    {
        return $this->column ?? '';
    }

    public function value(): string
    {
        return $this->value ?? '';
    }

    public function operator(): string
    {
        return $this->operator ?? '';
    }

    public function boolean(): string
    {
        return $this->boolean;
    }

    public function negated(): string
    {
        return $this->not ? 'NOT' : '';
    }

    public function toString(bool $first = false): string
    {
        return str(
            ' '.
            ($first ? '' : $this->boolean())
            .' '.
            $this->negated()
            .' '.
            $this->column()
            .' '.
            $this->operator()
            .' '.
            $this->value()
        )->squish();
    }
}