<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

trait HasEtag
{
    private string $etag = "";

    public function etag(): string
    {
        return $this->etag;
    }

    public function withEtag(string $etag): self
    {
        $clone = clone $this;
        $clone->etag = $etag;
        return $clone;
    }
}
