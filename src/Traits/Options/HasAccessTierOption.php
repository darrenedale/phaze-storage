<?php

namespace Phaze\Storage\Traits\Options;

use Phaze\Storage\Constants;
use Phaze\Storage\Types\AccessTier;

trait HasAccessTierOption
{
    abstract protected function option(string $name): mixed;

    abstract protected function setOption(string $name, mixed $value): void;

    abstract protected function clearOption(string $name): void;


    public function accessTier(): ?AccessTier
    {
        return $this->option(Constants::HeaderAccessTier);
    }

    public function withAccessTier(AccessTier $tier): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderAccessTier, $tier);
        return $clone;
    }

    public function withoutAccessTier(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderAccessTier);
        return $clone;
    }
}
