<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use Phaze\Storage\Types\EncryptionScope;

trait HasEncryptionScope
{
    private ?EncryptionScope $encryptionScope = null;

    public function encryptionScope(): ?EncryptionScope
    {
        return $this->encryptionScope;
    }

    public function withEncryptionScope(?EncryptionScope $scope): self
    {
        $clone = clone $this;
        $clone->encryptionScope = $scope;
        return $clone;
    }
}
