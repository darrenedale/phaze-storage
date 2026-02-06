<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

trait HasRestApiVersion
{
    private string $version = "";

    public function version(): string
    {
        return $this->version;
    }

    public function withVersion(string $version): self
    {
        $clone = clone $this;
        $clone->version = $version;
        return $clone;
    }
}
