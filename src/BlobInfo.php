<?php

declare(strict_types=1);

namespace Phaze\Storage;

use DateTimeInterface;
use LogicException;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Phaze\Common\Types\UnsignedInteger;
use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Storage\Traits\Blob\HasContentDetails;
use Phaze\Storage\Traits\Blob\HasCopyDetails;
use Phaze\Storage\Traits\Blob\HasCurrentVersionFlag;
use Phaze\Storage\Traits\Blob\HasEncryptionDetails;
use Phaze\Storage\Traits\Blob\HasLeaseDetails;
use Phaze\Storage\Traits\Blob\HasLegalHold;
use Phaze\Storage\Traits\Blob\HasMetadata;
use Phaze\Storage\Traits\Blob\HasName;
use Phaze\Storage\Traits\Blob\HasOwnershipAndPermissions;
use Phaze\Storage\Traits\Blob\HasResourceType;
use Phaze\Storage\Traits\Blob\HasSequenceNumber;
use Phaze\Storage\Traits\Blob\HasSnapshot;
use Phaze\Storage\Traits\Blob\HasTimestamps;
use Phaze\Storage\Traits\Blob\HasEtag;
use Phaze\Storage\Traits\Blob\HasVersionId;
use Phaze\Storage\Types\AccessTier;
use Phaze\Storage\Types\BlobType;
use Phaze\Storage\Types\RehydratePriority;
use Phaze\Storage\Types\TagCount;
use Stringable;

use function Phaze\Common\Utilities\Iterable\all;

class BlobInfo implements StringableContract, Stringable
{
    use HasName;
    use HasVersionId;
    use HasCurrentVersionFlag;
    use HasSnapshot;
    use HasSequenceNumber;
    use HasEtag;
    use HasContentDetails;
    use HasTimestamps;
    use HasOwnershipAndPermissions;
    use HasResourceType;
    use HasEncryptionDetails;
    use HasLeaseDetails;
    use HasCopyDetails;
    use HasMetadata;
    use HasLegalHold;
    use ImplementsPhpStringableViaPhazeStringable;

    private ?BlobType $type = null;

    private bool $incrementalCopy = false;

    private string $defaultEncryptionScope = "";

    private bool $denyEncryptionScopeOverride = false;

    private ?AccessTier $accessTier = null;

    private bool $accessTierInferred = false;

    private ?DateTimeInterface $accessTierChangeTime = null;

    private bool $deleted = false;

    private ?DateTimeInterface $deletedTime = null;

    private bool $hasImmutabilityPolicy = false;

    private bool $immutableStorageWithVersioningEnabled = false;

    private ?UnsignedInteger $remainingRetentionDays = null;

    private ?TagCount $tagCount = null;

    /** @var Tag[] */
    private array $tags = [];

    private string $contentCrc64 = "";

    private string $contentMd5 = "";

    private bool $placeholder = false;

    private ?RehydratePriority $rehydratePriority = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function __destruct()
    {
        $this->scrubCustomerProvidedKeySha256();
    }

    public function type(): ?BlobType
    {
        return $this->type;
    }

    public function withType(?BlobType $type): self
    {
        $clone = clone $this;
        $clone->type = $type;
        return $clone;
    }

    public function incrementalCopy(): bool
    {
        return $this->incrementalCopy;
    }

    public function withIncrementalCopy(bool $incremental): self
    {
        $clone = clone $this;
        $clone->incrementalCopy = $incremental;
        return $clone;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function withDeleted(bool $deleted): self
    {
        $clone = clone $this;
        $clone->deleted = $deleted;
        return $clone;
    }

    public function deletedTime(): ?DateTimeInterface
    {
        return $this->deletedTime;
    }

    public function withDeletedTime(?DateTimeInterface $time): self
    {
        $clone = clone $this;
        $clone->deletedTime = $time;
        return $clone;
    }

    public function denyEncryptionScopeOverride(): bool
    {
        return $this->denyEncryptionScopeOverride;
    }

    public function withDenyEncryptionScopeOverride(bool $denyOverride): self
    {
        $clone = clone $this;
        $clone->denyEncryptionScopeOverride = $denyOverride;
        return $clone;
    }

    public function hasImmutabilityPolicy(): bool
    {
        return $this->hasImmutabilityPolicy;
    }

    public function withImmutabilityPolicy(bool $hasPolicy): self
    {
        $clone = clone $this;
        $clone->hasImmutabilityPolicy = $hasPolicy;
        return $clone;
    }

    public function immutableStorageWithVersioningIsEnabled(): bool
    {
        return $this->immutableStorageWithVersioningEnabled;
    }

    public function withImmutableStorageWithVersioningIsEnabled(bool $enabled): self
    {
        $clone = clone $this;
        $clone->immutableStorageWithVersioningEnabled = $enabled;
        return $clone;
    }

    public function tagCount(): ?TagCount
    {
        return $this->tagCount;
    }

    public function withTagCount(?TagCount $count): self
    {
        $clone = clone $this;
        $clone->tagCount = $count;
        return $clone;
    }

    /** @return Tag[] */
    public function tags(): array
    {
        return $this->tags;
    }

    public function withTags(array $tags): self
    {
        assert(all($tags, fn (mixed $tag): bool => $tag instanceof Tag), new LogicException("Expected withTags() to be called with an array of Tag objects, found something that's not a Tag"));
        $clone = clone $this;
        $clone->tags = $tags;
        return $clone;
    }

    public function remainingRetentionDays(): ?UnsignedInteger
    {
        return $this->remainingRetentionDays;
    }

    public function withRemainingRetentionDays(?UnsignedInteger $days): self
    {
        $clone = clone $this;
        $clone->remainingRetentionDays = $days;
        return $clone;
    }

    public function contentCrc64(): string
    {
        return $this->contentCrc64;
    }

    public function withContentCrc64(string $contentCrc64): self
    {
        $clone = clone $this;
        $clone->contentCrc64 = $contentCrc64;
        return $clone;
    }

    public function contentMd5(): string
    {
        return $this->contentMd5;
    }

    public function withContentMd5(string $contentMd5): self
    {
        $clone = clone $this;
        $clone->contentMd5 = $contentMd5;
        return $clone;
    }

    public function accessTier(): ?AccessTier
    {
        return $this->accessTier;
    }

    public function withAccessTier(?AccessTier $accessTier): self
    {
        $clone = clone $this;
        $clone->accessTier = $accessTier;
        return $clone;
    }

    public function accessTierInferred(): bool
    {
        return $this->accessTierInferred;
    }

    public function withAccessTierInferred(bool $inferred): self
    {
        $clone = clone $this;
        $clone->accessTierInferred = $inferred;
        return $clone;
    }

    public function accessTierChangeTime(): ?DateTimeInterface
    {
        return $this->accessTierChangeTime;
    }

    public function withAccessTierChangeTime(?DateTimeInterface $time): self
    {
        $clone = clone $this;
        $clone->accessTierChangeTime = $time;
        return $clone;
    }

    public function placeholder(): bool
    {
        return $this->placeholder;
    }

    public function withPlaceholder(bool $placeholder): self
    {
        $clone = clone $this;
        $clone->placeholder = $placeholder;
        return $clone;
    }

    public function rehydratePriority(): ?RehydratePriority
    {
        return $this->rehydratePriority;
    }

    public function withRehydratePriority(?RehydratePriority $priority): self
    {
        $clone = clone $this;
        $clone->rehydratePriority = $priority;
        return $clone;
    }

    public function toString(): string
    {
        return $this->name();
    }
}
