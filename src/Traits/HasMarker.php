<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits;

trait HasMarker
{
    private string $marker = "";

    public function marker(): string
    {
        return $this->marker;
    }

    public function withMarker(string $marker): self
    {
        $clone = clone $this;
        $clone->marker = $marker;
        return $clone;
    }
}
