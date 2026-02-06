<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use Phaze\Storage\Types\EncryptionContext;
use Phaze\Storage\Types\EncryptionScope;

use function Phaze\Common\Utilities\String\scrub;

trait HasEncryptionDetails
{
    use HasEncryptionScope;

    private ?EncryptionContext $encryptionContext = null;

    private ?EncryptionScope $encryptionScope = null;

    private bool $serverEncrypted = false;

    private ?string $customerProvidedKeySha256 = null;

    final protected function scrubCustomerProvidedKeySha256(): void
    {
        if (null === $this->customerProvidedKeySha256) {
            return;
        }

        scrub($this->customerProvidedKeySha256);
        $this->customerProvidedKeySha256 = null;
    }

    public function serverEncrypted(): bool
    {
        return $this->serverEncrypted;
    }

    public function withServerEncrypted(bool $encrypted): self
    {
        $clone = clone $this;
        $clone->serverEncrypted = $encrypted;
        return $clone;
    }

    public function encryptionContext(): ?EncryptionContext
    {
        return $this->encryptionContext;
    }

    public function withEncryptionContext(?EncryptionContext $context): self
    {
        $clone = clone $this;
        $clone->encryptionContext = $context;
        return $clone;
    }

    public function customerProvidedKeySha256(): ?string
    {
        return $this->customerProvidedKeySha256;
    }

    public function withCustomerProvidedKeySha256(?string $customerProvidedKeySha256): self
    {
        $clone = clone $this;
        $clone->scrubCustomerProvidedKeySha256();
        $clone->customerProvidedKeySha256 = $customerProvidedKeySha256;
        return $clone;
    }

}
