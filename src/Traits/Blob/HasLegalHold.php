<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

trait HasLegalHold
{
    private bool $hasLegalHold = false;

    public function legalHold(): bool
    {
        return $this->hasLegalHold;
    }

    public function withLegalHold(bool $hasLegalHold): self
    {
        $clone = clone $this;
        $clone->hasLegalHold = $hasLegalHold;
        return $clone;
    }
}
