<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use DateTimeInterface;

trait HasTimestamps
{
    use HasLastModified;

    private ?DateTimeInterface $creationTime = null;

    private ?DateTimeInterface $lastAccessed = null;

    private ?DateTimeInterface $expiryTime = null;

    public function creationTime(): ?DateTimeInterface
    {
        return $this->creationTime;
    }

    public function withCreationTime(?DateTimeInterface $creationTime): self
    {
        $clone = clone $this;
        $clone->creationTime = $creationTime;
        return $clone;
    }

    public function lastAccessed(): ?DateTimeInterface
    {
        return $this->lastAccessed;
    }

    public function withLastAccessed(?DateTimeInterface $lastAccessed): self
    {
        $clone = clone $this;
        $clone->lastAccessed = $lastAccessed;
        return $clone;
    }

    public function expiryTime(): ?DateTimeInterface
    {
        return $this->expiryTime;
    }

    public function withExpiryTime(?DateTimeInterface $expiry): self
    {
        $clone = clone $this;
        $clone->expiryTime = $expiry;
        return $clone;
    }
}
