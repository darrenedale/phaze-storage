<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits;

trait HasNextMarker
{
    private string $nextMarker = "";

    public function nextMarker(): string
    {
        return $this->nextMarker;
    }

    public function withNextMarker(string $marker): self
    {
        $clone = clone $this;
        $clone->nextMarker = $marker;
        return $clone;
    }
}
