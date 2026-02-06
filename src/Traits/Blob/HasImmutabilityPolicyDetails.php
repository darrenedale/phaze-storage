<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use DateTimeInterface;
use Phaze\Storage\Types\ImmutabilityPolicyMode;

trait HasImmutabilityPolicyDetails
{
    private ?ImmutabilityPolicyMode $immutabilityPolicyMode;

    private ?DateTimeInterface $immutabilityPolicyUntil;

    public function immutabilityPolicyMode(): ?ImmutabilityPolicyMode
    {
        return $this->immutabilityPolicyMode;
    }

    public function withImmutabilityPolicyMode(?ImmutabilityPolicyMode $mode): self
    {
        $clone = clone $this;
        $clone->immutabilityPolicyMode = $mode;
        return $clone;
    }

    public function immutabilityPolicyUntil(): ?DateTimeInterface
    {
        return $this->immutabilityPolicyUntil;
    }

    public function withImmutabilityPolicyUntil(?DateTimeInterface $until): self
    {
        $clone = clone $this;
        $clone->immutabilityPolicyUntil = $until;
        return $clone;
    }

}