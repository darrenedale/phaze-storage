<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use function Phaze\Common\Utilities\String\scrub;

trait HasEncryptionKeySha256
{
    private ?string $encryptionKeySha256 = null;

    final protected function scrubEncryptionKeySha256(): void
    {
        if (null === $this->encryptionKeySha256) {
            return;
        }

        scrub($this->encryptionKeySha256);
        $this->encryptionKeySha256 = null;
    }

    public function encryptionKeySha256(): string
    {
        return $this->encryptionKeySha256;
    }

    public function withEncryptionKeySha256(?string $hash): self
    {
        $clone = clone $this;
        $clone->encryptionKeySha256 = $hash;
        return $clone;
    }
}
