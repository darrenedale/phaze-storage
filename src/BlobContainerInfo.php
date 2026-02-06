<?php

declare(strict_types=1);

namespace Phaze\Storage;

use DateTimeInterface;
use InvalidArgumentException;
use Phaze\Storage\Types\ContainerName;
use Stringable as StringableContract;

use function Phaze\Common\Utilities\Iterable\all;

class BlobContainerInfo implements StringableContract
{
    private ?DateTimeInterface $lastModified = null;

    private ContainerName $name;

    private string $etag = "";

    private string $leaseStatus = "";

    private string $leaseState = "";

    private string $defaultEncryptionScope = "";

    private ?string $version = null;

    private bool $deleted = false;

    private bool $hasImmutabilityPolicy = false;

    private bool $denyEncryptionScopeOverride = false;

    private bool $hasLegalHold = false;

    private bool $immutableStorageWithVersioningEnabled = false;

    /** @var array<string,string>  */
    private array $metadata = [];

    public function __construct(ContainerName $name)
    {
        $this->name = $name;
    }

    public function name(): ContainerName
    {
        return $this->name;
    }

    public function withName(ContainerName $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    public function lastModified(): ?DateTimeInterface
    {
        return $this->lastModified;
    }

    public function withLastModified(?DateTimeInterface $lastModified): self
    {
        $clone = clone $this;
        $clone->lastModified = $lastModified;
        return $clone;
    }

    public function etag(): string
    {
        return $this->etag;
    }

    public function withEtag(string $etag): self
    {
        $clone = clone $this;
        $clone->etag = $etag;
        return $clone;
    }

    public function leastStatus(): string
    {
        return $this->leaseStatus;
    }

    public function withLeaseStatus(string $leaseStatus): self
    {
        $clone = clone $this;
        $clone->leaseStatus = $leaseStatus;
        return $clone;
    }

    public function leastState(): string
    {
        return $this->leaseState;
    }

    public function withLeaseState(string $leaseState): self
    {
        $clone = clone $this;
        $clone->leaseState = $leaseState;
        return $clone;
    }

    public function defaultEncryptionScope(): string
    {
        return $this->defaultEncryptionScope;
    }

    public function withDefaultEncryptionScope(string $scope): self
    {
        $clone = clone $this;
        $clone->defaultEncryptionScope = $scope;
        return $clone;
    }

    public function version(): ?string
    {
        return $this->version;
    }

    public function withVersion(?string $version): self
    {
        $clone = clone $this;
        $clone->version = $version;
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

    public function hasLegalHold(): bool
    {
        return $this->hasLegalHold;
    }

    public function withLegalHold(bool $hasLegalHold): self
    {
        $clone = clone $this;
        $clone->hasLegalHold = $hasLegalHold;
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

    /** @return array<string,string> */
    public function metadata(): array
    {
        return $this->metadata;
    }

    public function withMetadata(string $name, string $value): self
    {
        $clone = clone $this;
        $clone->metadata[$name] = $value;
        return $clone;
    }

    public function withReplacementMetadata(array $data): self
    {
        if (!all($data, fn (mixed $value, mixed $key): bool => is_string($key) && is_string($value))) {
            throw new InvalidArgumentException("Expected a dictionary of string keys and values, found a non-string key or value");
        }

        $clone = clone $this;
        $clone->metadata = $data;
        return $clone;
    }

    public function __toString(): string
    {
        return (string) $this->name();
    }
}
