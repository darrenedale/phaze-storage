<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Options;

trait HasTimeoutOption
{
    abstract public function option(string $name): mixed;

    abstract protected function setOption(string $name, mixed $value): void;

    abstract protected function clearOption(string $name): void;

    public function timeout(): ?int
    {
        return $this->option("timeout");
    }

    public function withTimeout(int $timeout): self
    {
        $clone = clone $this;
        $clone->setOption("timeout", $timeout);
        return $clone;
    }

    public function withoutTimeout(): self
    {
        $clone = clone $this;
        $clone->clearOption("timeout");
        return $clone;
    }
}
