<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

trait HasVersionId
{
    private string $versionId = "";

    public function versionId(): string
    {
        return $this->versionId;
    }

    public function withVersionId(string $versionId): self
    {
        $clone = clone $this;
        $clone->versionId = $versionId;
        return $clone;
    }
}
