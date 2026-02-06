<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use Phaze\Common\Types\UnsignedInteger;

trait HasCommittedBlockCount
{
    private ?UnsignedInteger $committedBlockCount = null;

    // only append blobs
    public function committedBlockCount(): ?UnsignedInteger
    {
        return $this->committedBlockCount;
    }

    public function withCommittedBlockCount(?UnsignedInteger $count): self
    {
        $clone = clone $this;
        $clone->committedBlockCount = $count;
        return $clone;
    }
}
