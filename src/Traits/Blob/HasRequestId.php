<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

trait HasRequestId
{
    private string $requestId = "";

    public function requestId(): string
    {
        return $this->requestId;
    }

    public function withRequestId(string $requestId): self
    {
        $clone = clone $this;
        $clone->requestId = $requestId;
        return $clone;
    }
}
