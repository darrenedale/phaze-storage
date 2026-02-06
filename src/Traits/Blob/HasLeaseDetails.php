<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use Phaze\Storage\Types\LeaseDurationType;
use Phaze\Storage\Types\LeaseState;
use Phaze\Storage\Types\LeaseStatus;

trait HasLeaseDetails
{
    private ?LeaseStatus $leaseStatus = null;

    private ?LeaseState $leaseState = null;

    private ?LeaseDurationType $leaseDuration = null;

    public function leaseStatus(): ?LeaseStatus
    {
        return $this->leaseStatus;
    }

    public function withLeaseStatus(LeaseStatus $leaseStatus): self
    {
        $clone = clone $this;
        $clone->leaseStatus = $leaseStatus;
        return $clone;
    }

    public function leaseState(): ?LeaseState
    {
        return $this->leaseState;
    }

    public function withLeaseState(?LeaseState $leaseState): self
    {
        $clone = clone $this;
        $clone->leaseState = $leaseState;
        return $clone;
    }

    public function leaseDuration(): ?LeaseDurationType
    {
        return $this->leaseDuration;
    }

    public function withLeaseDuration(?LeaseDurationType $duration): self
    {
        $clone = clone $this;
        $clone->leaseDuration = $duration;
        return $clone;
    }
}
