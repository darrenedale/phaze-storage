<?php

namespace Phaze\Storage\Traits\Options;

use Phaze\Storage\Constants;

trait HasLegalHoldOption
{
    abstract protected function option(string $name): mixed;

    abstract protected function setOption(string $name, mixed $value): void;

    abstract protected function clearOption(string $name): void;


    public function legalHold(): ?bool
    {
        return $this->option(Constants::HeaderLegalHold);
    }

    public function withLegalHold(bool $hold): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderLegalHold, $hold);
        return $clone;
    }


    public function withoutLegalHold(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderLegalHold);
        return $clone;
    }
}
