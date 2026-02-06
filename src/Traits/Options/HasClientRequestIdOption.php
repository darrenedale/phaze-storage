<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Options;

trait HasClientRequestIdOption
{
    abstract public function option(string $name): mixed;

    abstract protected function setOption(string $name, mixed $value): void;

    public function clientRequestId(): ?string
    {
        return $this->option("x-ms-client-request-id");
    }

    public function withClientRequestId(?string $id): self
    {
        $clone = clone $this;
        $clone->setOption("x-ms-request-id", $id);
        return $clone;
    }
}
