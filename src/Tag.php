<?php

declare(strict_types=1);

namespace Phaze\Storage;

class Tag
{
    private string $key;

    private string $value;

    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function withKey(string $key): self
    {
        $clone = clone $this;
        $clone->key = $key;
        return $clone;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function withValue(string $value): self
    {
        $clone = clone $this;
        $clone->value = $value;
        return $clone;
    }
}
