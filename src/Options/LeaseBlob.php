<?php

declare(strict_types=1);

namespace Phaze\Storage\Options;

use Phaze\Common\Types\Guid;
use Phaze\Storage\Constants;
use Phaze\Storage\Traits\Options\HasLeaseIdOption;
use Phaze\Storage\Types\LeaseBreakPeriod;
use Phaze\Storage\Types\LeaseDuration;
use Phaze\Storage\Traits\Options\FiltersOptionsByKey;
use Phaze\Storage\Traits\Options\HasTimeoutOption;

class LeaseBlob extends AbstractOptions
{
    use HasTimeoutOption;
    use HasLeaseIdOption;
    use FiltersOptionsByKey;

    public function proposedLeaseId(): ?Guid
    {
        return $this->option(Constants::HeaderProposedLeaseId);
    }

    public function withProposedLeaseId(Guid $leaseId): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderProposedLeaseId, $leaseId);
        return $clone;
    }

    public function withoutProposedLeaseId(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderProposedLeaseId);
        return $clone;
    }

    public function leaseDuration(): ?LeaseDuration
    {
        return $this->option(Constants::HeaderLeaseDuration);
    }

    public function withLeaseDuration(LeaseDuration $duration): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderLeaseDuration, $duration);
        return $clone;
    }

    public function withoutLeaseDuration(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderLeaseDuration);
        return $clone;
    }

    public function leaseBreakPeriod(): ?LeaseBreakPeriod
    {
        return $this->option(Constants::HeaderLeaseBreakPeriod);
    }

    public function withLeaseBreakPeriod(LeaseBreakPeriod $duration): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderLeaseBreakPeriod, $duration);
        return $clone;
    }

    public function withoutLeaseBreakPeriod(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderLeaseBreakPeriod);
        return $clone;
    }
}
