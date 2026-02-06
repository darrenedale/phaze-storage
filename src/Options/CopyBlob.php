<?php

declare(strict_types=1);

namespace Phaze\Storage\Options;

use DateTimeInterface;
use Phaze\Common\Types\Guid;
use Phaze\Storage\Constants;
use Phaze\Storage\Traits\Options\HasAccessTierOption;
use Phaze\Storage\Traits\Options\HasCustomerEncryptionOptions;
use Phaze\Storage\Traits\Options\HasLeaseIdOption;
use Phaze\Storage\Traits\Options\HasLegalHoldOption;
use Phaze\Storage\Traits\Options\HasMetadataOption;
use Phaze\Storage\Traits\Options\HasTagsOption;
use Phaze\Storage\Types\RehydratePolicy;
use Phaze\Storage\Traits\Options\ExposesAllOptions;
use Phaze\Storage\Traits\Options\FiltersOptionsByKey;
use Phaze\Storage\Traits\Options\HasTimeoutOption;

class CopyBlob extends AbstractOptions
{
    use HasAccessTierOption;
    use HasLeaseIdOption;
    use HasCustomerEncryptionOptions;
    use HasMetadataOption;
    use HasLegalHoldOption;
    use HasTagsOption;
    use HasTimeoutOption;
    use FiltersOptionsByKey;
    use ExposesAllOptions;

    public function sourceLeaseId(): ?Guid
    {
        return $this->option(Constants::HeaderSourceLeaseId);
    }

    public function withSourceLeaseId(Guid $leaseId): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderSourceLeaseId, $leaseId);
        return $clone;
    }

    public function withoutSourceLeaseId(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderSourceLeaseId);
        return $clone;
    }

    public function sealBlob(): ?bool
    {
        return $this->option(Constants::HeaderSealBlob);
    }

    public function withSealBlob(bool $seal): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderSealBlob, $seal);
        return $clone;
    }

    public function withoutSealBlob(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderSealBlob);
        return $clone;
    }

    public function rehydratePolicy(): ?RehydratePolicy
    {
        return $this->option(Constants::HeaderRehydratePolicy);
    }

    public function withRehydratePolicy(RehydratePolicy $policy): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderRehydratePolicy, $policy);
        return $clone;
    }

    public function withoutRehydratePolicy(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderRehydratePolicy);
        return $clone;
    }

    public function immutabilityPolicyUntilDate(): ?DateTimeInterface
    {
        return $this->option(Constants::HeaderImmutabilityPolicyUntilDate);
    }

    public function withImmutabilityPolicyUntilDate(DateTimeInterface $UntilDate): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderImmutabilityPolicyUntilDate, $UntilDate);
        return $clone;
    }

    public function withoutImmutabilityPolicyUntilDate(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderImmutabilityPolicyUntilDate);
        return $clone;
    }
}
