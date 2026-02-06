<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

trait HasSealed
{
    private bool $sealed = false;

    public function sealed(): bool
    {
        return $this->sealed;
    }

    public function withSealed(bool $sealed): self
    {
        $clone = clone $this;
        $clone->sealed = $sealed;
        return $clone;
    }
}
