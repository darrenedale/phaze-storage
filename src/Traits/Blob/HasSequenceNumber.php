<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

trait HasSequenceNumber
{
    private ?int $sequenceNumber = null;

    public function sequenceNumber(): ?int
    {
        return $this->sequenceNumber;
    }

    public function withSequenceNumber(?int $number): self
    {
        $clone = clone $this;
        $clone->sequenceNumber = $number;
        return $clone;
    }
}
