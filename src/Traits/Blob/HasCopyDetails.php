<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use DateTimeInterface;
use Phaze\Storage\Types\CopyProgress;

trait HasCopyDetails
{
    use HasCopyId;
    use HasCopyStatus;

    private string $copyStatusDescription = "";

    private string $copySource = "";

    private ?CopyProgress $copyProgress = null;

    private ?DateTimeInterface $copyCompletionTime = null;

    public function copyStatusDescription(): string
    {
        return $this->copyStatusDescription;
    }

    public function withCopyStatusDescription(string $description): self
    {
        $clone = clone $this;
        $clone->copyStatusDescription = $description;
        return $clone;
    }

    public function copySource(): string
    {
        return $this->copySource;
    }

    public function withCopySource(string $source): self
    {
        $clone = clone $this;
        $clone->copySource = $source;
        return $clone;
    }

    public function copyProgress(): ?CopyProgress
    {
        return $this->copyProgress;
    }

    public function withCopyProgress(?CopyProgress $progress): self
    {
        $clone = clone $this;
        $clone->copyProgress = $progress;
        return $clone;
    }

    public function copyCompletionTime(): ?DateTimeInterface
    {
        return $this->copyCompletionTime;
    }

    public function withCopyCompletionTime(?DateTimeInterface $time): self
    {
        $clone = clone $this;
        $clone->copyCompletionTime = $time;
        return $clone;
    }
}
