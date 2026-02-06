<?php

namespace Phaze\Storage\Traits\Options;

use Phaze\Storage\Constants;
use Phaze\Storage\Types\EncryptionScope;

trait HasEncryptionScopeOption
{
    abstract protected function option(string $name): mixed;

    abstract protected function setOption(string $name, mixed $value): void;

    abstract protected function clearOption(string $name): void;

    public function encryptionScope(): ?EncryptionScope
    {
        return $this->option(Constants::HeaderEncryptionScope);
    }

    public function withEncryptionScope(EncryptionScope $scope): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderEncryptionScope, $scope);
        return $clone;
    }

    public function withoutEncryptionScope(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderEncryptionScope);
        return $clone;
    }
}
