<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

trait HasCopyId
{
    private string $copyId = "";

    public function copyId(): string
    {
        return $this->copyId;
    }

    public function withCopyId(string $id): self
    {
        $clone = clone $this;
        $clone->copyId = $id;
        return $clone;
    }
}