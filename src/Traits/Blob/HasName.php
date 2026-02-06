<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

trait HasName
{
    private string $name;

    public function name(): string
    {
        return $this->name;
    }

    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }
}
