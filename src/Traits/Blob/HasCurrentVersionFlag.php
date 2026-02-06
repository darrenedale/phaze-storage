<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

trait HasCurrentVersionFlag
{
    private bool $isCurrentVersion = false;

    public function isCurrentVersion(): bool
    {
        return $this->isCurrentVersion;
    }

    public function withCurrentVersion(bool $current): self
    {
        $clone = clone $this;
        $clone->isCurrentVersion = $current;
        return $clone;
    }
}
