<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use Phaze\Storage\Types\ResourceType;

trait HasResourceType
{
    private ?ResourceType $resourceType = null;

    public function resourceType(): ?ResourceType
    {
        return $this->resourceType;
    }

    public function withResourceType(?ResourceType $resourceType): self
    {
        $clone = clone $this;
        $clone->resourceType = $resourceType;
        return $clone;
    }

}
