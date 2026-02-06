<?php

namespace Phaze\Storage\Traits\Options;

use Phaze\Storage\Constants;
use Phaze\Common\Types\Guid;

trait HasLeaseIdOption
{
    abstract protected function option(string $name): mixed;

    abstract protected function setOption(string $name, mixed $value): void;

    abstract protected function clearOption(string $name): void;

    public function leaseId(): ?Guid
    {
        return $this->option(Constants::HeaderLeaseId);
    }

    public function withLeaseId(Guid $leaseId): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderLeaseId, $leaseId);
        return $clone;
    }

    public function withoutLeaseId(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderLeaseId);
        return $clone;
    }
}
