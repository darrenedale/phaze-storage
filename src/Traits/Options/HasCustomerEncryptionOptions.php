<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Options;

use Phaze\Storage\Constants;

trait HasCustomerEncryptionOptions
{
    abstract protected function option(string $name): mixed;

    abstract protected function setOption(string $name, mixed $value): void;

    abstract protected function clearOption(string $name): void;

    abstract protected function scrubOption(string $name): void;

    final protected function scrubEncryptionKey(): void
    {
        $this->scrubOption(Constants::HeaderEncryptionKey);
    }

    final protected function scrubEncryptionKeySha256(): void
    {
        $this->scrubOption(Constants::HeaderEncryptionKeySha256);
    }

    public function encryptionKey(): ?string
    {
        return $this->option(Constants::HeaderEncryptionKey);
    }

    public function withEncryptionKey(string $key): self
    {
        $clone = clone $this;
        $clone->scrubEncryptionKey();
        $clone->setOption(Constants::HeaderEncryptionKey, $key);
        return $clone;
    }

    public function withoutEncryptionKey(): self
    {
        $clone = clone $this;
        $clone->scrubEncryptionKey();
        return $clone;
    }

    public function encryptionKeySha256(): ?string
    {
        return $this->option(Constants::HeaderEncryptionKeySha256);
    }

    public function withEncryptionKeySha256(string $key): self
    {
        $clone = clone $this;
        $clone->scrubEncryptionKeySha256();
        $clone->setOption(Constants::HeaderEncryptionKeySha256, $key);
        return $clone;
    }

    public function withoutEncryptionKeySha256(): self
    {
        $clone = clone $this;
        $clone->scrubEncryptionKeySha256();
        return $clone;
    }

    public function encryptionAlgorithm(): ?string
    {
        return $this->option(Constants::HeaderEncryptionAlgorithm);
    }

    public function withEncryptionAlgorithm(string $algorithm): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderEncryptionAlgorithm, $algorithm);
        return $clone;
    }

    public function withoutEncryptionAlgorithm(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderEncryptionAlgorithm);
        return $clone;
    }
}
