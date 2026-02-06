<?php

declare(strict_types=1);

namespace Phaze\Storage\Responses;

use ArrayIterator;
use IteratorAggregate;
use Phaze\Common\Contracts\Types\Arrayable as ArrayableContract;
use Phaze\Storage\Contracts\ResultsList as ResultsListContract;
use Phaze\Storage\BlobInfo;
use Phaze\Storage\Traits\HasMarker;
use Phaze\Storage\Traits\HasNextMarker;
use Phaze\Storage\Traits\NextMarkerSignifiesCompleteness;
use Traversable;

/**
 * @implements IteratorAggregate<BlobInfo>
 */
class ListBlobs implements IteratorAggregate, ResultsListContract, ArrayableContract
{
    use HasMarker;
    use HasNextMarker;
    use NextMarkerSignifiesCompleteness;

    private ?int $maxResults = null;

    private array $blobs;

    private string $blobPrefix = "";

    public function __construct(array $blobs, string $nextMarker = "")
    {
        $this->blobs = $blobs;
        $this->nextMarker = $nextMarker;
    }

    public function maxResults(): ?int
    {
        return $this->maxResults;
    }

    public function withMaxResults(?int $max): self
    {
        $clone = clone $this;
        $clone->maxResults = $max;
        return $clone;
    }

    public function blobPrefix(): string
    {
        return $this->blobPrefix;
    }

    public function withBlobPrefix(string $prefix): self
    {
        $clone = clone $this;
        $clone->blobPrefix = $prefix;
        return $clone;
    }

    /** @return BlobInfo[] */
    public function toArray(): array
    {
        return $this->blobs;
    }

    /** @return Traversable<BlobInfo> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->blobs);
    }
}
