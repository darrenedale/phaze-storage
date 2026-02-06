<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use Phaze\Common\Types\Range;

trait HasRange
{
    private ?Range $range = null;

    public function range(): ?Range
    {
        return $this->range;
    }

    public function withRange(?Range $range): self
    {
        $clone = clone $this;
        $clone->range = $range;
        return $clone;
    }
}
