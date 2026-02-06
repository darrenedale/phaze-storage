<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use DateTimeInterface;

trait HasLastModified
{
    private ?DateTimeInterface $lastModified = null;

    public function lastModified(): ?DateTimeInterface
    {
        return $this->lastModified;
    }

    public function withLastModified(?DateTimeInterface $lastModified): self
    {
        $clone = clone $this;
        $clone->lastModified = $lastModified;
        return $clone;
    }
}
