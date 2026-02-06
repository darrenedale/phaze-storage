<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use DateTimeInterface;

trait HasLastAccessTime
{
    private ?DateTimeInterface $lastAccessTime = null;

    public function lastAccessTime(): ?DateTimeInterface
    {
        return $this->lastAccessTime;
    }

    public function withLastAccessTime(?DateTimeInterface $lastModified): self
    {
        $clone = clone $this;
        $clone->lastAccessTime = $lastModified;
        return $clone;
    }
}
