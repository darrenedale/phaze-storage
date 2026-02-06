<?php

declare(strict_types=1);

namespace Phaze\Storage\Contracts;

use DateTimeInterface;
use Phaze\Common\Types\UnsignedInteger;
use Phaze\Storage\Types\BlobType;
use Phaze\Storage\Types\CopyProgress;
use Phaze\Storage\Types\CopyStatus;
use Phaze\Storage\Types\EncryptionContext;
use Phaze\Storage\Types\EncryptionScope;
use Phaze\Storage\Types\ImmutabilityPolicyMode;
use Phaze\Storage\Types\LeaseDurationType;
use Phaze\Storage\Types\LeaseState;
use Phaze\Storage\Types\LeaseStatus;
use Phaze\Storage\Types\Permissions;
use Phaze\Storage\Types\ResourceType;
use Phaze\Storage\Types\TagCount;

interface BlobProperties extends BlobMetadata
{
    public function name(): string;

    public function withName(string $name): self;

    public function versionId(): string;

    public function withVersionId(string $versionId): self;

    public function isCurrentVersion(): bool;

    public function withCurrentVersion(bool $current): self;

    public function snapshot(): string;

    public function withSnapshot(string $snapshot): self;

    public function sequenceNumber(): ?int;

    public function withSequenceNumber(?int $number): self;

    public function etag(): string;

    public function withEtag(string $etag): self;

    public function contentType(): string;

    public function withContentType(string $contentType): self;

    public function contentEncoding(): string;

    public function withContentEncoding(string $encoding): self;

    public function contentLength(): ?UnsignedInteger;

    public function withContentLength(?UnsignedInteger $length): self;

    public function contentLanguage(): string;

    public function withContentLanguage(string $language): self;

    public function contentDisposition(): string;

    public function withContentDisposition(string $contentDisposition): self;

    public function cacheControl(): string;

    public function withCacheControl(string $cacheControl): self;

    public function creationTime(): ?DateTimeInterface;

    public function withCreationTime(?DateTimeInterface $creationTime): self;

    public function lastModified(): ?DateTimeInterface;

    public function withLastModified(?DateTimeInterface $lastModified): self;

    public function lastAccessed(): ?DateTimeInterface;

    public function withLastAccessed(?DateTimeInterface $lastAccessed): self;

    public function expiryTime(): ?DateTimeInterface;

    public function withExpiryTime(?DateTimeInterface $expiry): self;

    public function owner(): string;

    public function withOwner(string $owner): self;

    public function group(): string;

    public function withGroup(string $group): self;

    public function permissions(): ?Permissions;

    public function withPermissions(?Permissions $permissions): self;

    public function resourceType(): ?ResourceType;

    public function withResourceType(?ResourceType $resourceType): self;

    public function serverEncrypted(): bool;

    public function withServerEncrypted(bool $encrypted): self;

    public function encryptionContext(): ?EncryptionContext;

    public function withEncryptionContext(?EncryptionContext $context): self;

    public function encryptionScope(): ?EncryptionScope;

    public function withEncryptionScope(?EncryptionScope $scope): self;

    public function customerProvidedKeySha256(): ?string;

    public function withCustomerProvidedKeySha256(?string $customerProvidedKeySha256): self;

    public function leaseStatus(): ?LeaseStatus;

    public function withLeaseStatus(LeaseStatus $leaseStatus): self;

    public function leaseState(): ?LeaseState;

    public function withLeaseState(?LeaseState $leaseState): self;

    public function leaseDuration(): ?LeaseDurationType;

    public function withLeaseDuration(?LeaseDurationType $duration): self;

    public function copyId(): string;

    public function withCopyId(string $id): self;

    public function copyStatus(): ?CopyStatus;

    public function withCopyStatus(?CopyStatus $status): self;

    public function copyStatusDescription(): string;

    public function withCopyStatusDescription(string $description): self;

    public function copySource(): string;

    public function withCopySource(string $source): self;

    public function copyProgress(): ?CopyProgress;

    public function withCopyProgress(?CopyProgress $progress): self;

    public function copyCompletionTime(): ?DateTimeInterface;

    public function withCopyCompletionTime(?DateTimeInterface $time): self;

    public function legalHold(): bool;

    public function withLegalHold(bool $hasLegalHold): self;

    public function immutabilityPolicyMode(): ?ImmutabilityPolicyMode;

    public function withImmutabilityPolicyMode(?ImmutabilityPolicyMode $mode): self;

    public function immutabilityPolicyUntil(): ?DateTimeInterface;

    public function withImmutabilityPolicyUntil(?DateTimeInterface $until): self;

    public function type(): BlobType;

    public function withType(BlobType $type): self;

    public function tagCount(): ?TagCount;

    public function withTagCount(?TagCount $count): self;

    public function contentCrc64(): string;

    public function withContentCrc64(string $contentCrc64): self;

    public function contentMd5(): string;

    public function withContentMd5(string $contentMd5): self;

    public function fullBlobMd5(): string;

    public function withFullBlobMd5(string $md5): self;

    // only append blobs
    public function committedBlockCount(): ?UnsignedInteger;

    public function withCommittedBlockCount(?UnsignedInteger $count): self;

    public function sealed(): bool;

    public function withSealed(bool $sealed): self;
}
