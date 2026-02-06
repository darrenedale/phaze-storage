<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use DateTimeInterface;

trait HasCreationTime
{
    private ?DateTimeInterface $creationTime = null;

    public function creationTime(): ?DateTimeInterface
    {
        return $this->creationTime;
    }

    public function withCreationTime(?DateTimeInterface $lastModified): self
    {
        $clone = clone $this;
        $clone->creationTime = $lastModified;
        return $clone;
    }
}
