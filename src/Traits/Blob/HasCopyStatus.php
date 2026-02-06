<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use Phaze\Storage\Types\CopyStatus;

trait HasCopyStatus
{
    private ?CopyStatus $copyStatus = null;

    public function copyStatus(): ?CopyStatus
    {
        return $this->copyStatus;
    }

    public function withCopyStatus(?CopyStatus $status): self
    {
        $clone = clone $this;
        $clone->copyStatus = $status;
        return $clone;
    }
}