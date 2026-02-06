<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use DateTimeInterface;

trait HasDate
{
    private ?DateTimeInterface $date = null;

    public function date(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function withDate(?DateTimeInterface $date): self
    {
        $clone = clone $this;
        $clone->date = $date;
        return $clone;
    }
}
