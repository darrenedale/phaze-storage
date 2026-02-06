<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Options;

trait HasSnapshotAndVersionOptions
{
    abstract public function option(string $name): mixed;

    abstract protected function setOption(string $name, mixed $value): void;

    abstract protected function clearOption(string $name): void;

    public function versionId(): ?string
    {
        return $this->option("versionid");
    }

    public function withVersionId(string $versionId): self
    {
        $clone = clone $this;
        $clone->setOption("versionid", $versionId);
        return $clone;
    }

    public function withoutVersionId(): self
    {
        $clone = clone $this;
        $clone->$this->clearOption("versionid");
        return $clone;
    }

    public function snapshot(): ?string
    {
        return $this->option("snapshot");
    }

    public function withSnapshot(string $snapshot): self
    {
        $clone = clone $this;
        $clone->setOption("snapshot", $snapshot);
        return $clone;
    }

    public function withoutSnapshot(): self
    {
        $clone = clone $this;
        $clone->$this->clearOption("snapshot");
        return $clone;
    }

}
