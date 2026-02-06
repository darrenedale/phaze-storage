<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use Phaze\Storage\Types\TagCount;

trait HasTagCount
{
    private ?TagCount $tagCount = null;


    public function tagCount(): ?TagCount
    {
        return $this->tagCount;
    }

    public function withTagCount(?TagCount $count): self
    {
        $clone = clone $this;
        $clone->tagCount = $count;
        return $clone;
    }
}
