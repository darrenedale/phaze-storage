<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

trait HasSnapshot
{
    private string $snapshot = "";

    public function snapshot(): string
    {
        return $this->snapshot;
    }

    public function withSnapshot(string $snapshot): self
    {
        $clone = clone $this;
        $clone->snapshot = $snapshot;
        return $clone;
    }
}
