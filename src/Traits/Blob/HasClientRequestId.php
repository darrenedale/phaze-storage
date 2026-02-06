<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

trait HasClientRequestId
{
    private string $clientRequestId = "";

    public function clientRequestId(): string
    {
        return $this->clientRequestId;
    }

    public function withClientRequestId(string $clientRequestId): self
    {
        $clone = clone $this;
        $clone->clientRequestId = $clientRequestId;
        return $clone;
    }
}
