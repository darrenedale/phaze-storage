<?php

declare(strict_types=1);

namespace Phaze\Storage\Responses;

use ArrayIterator;
use IteratorAggregate;
use Phaze\Storage\Contracts\ResultsList as ResultsListContract;
use Phaze\common\Contracts\Types\Arrayable as ArrayableContract;
use Phaze\Storage\BlobContainerInfo;
use Phaze\Storage\Traits\HasMarker;
use Phaze\Storage\Traits\HasNextMarker;
use Phaze\Storage\Traits\NextMarkerSignifiesCompleteness;
use Traversable;

/**
 * @implements IteratorAggregate<BlobContainerInfo>
 */
class ListContainers implements IteratorAggregate, ResultsListContract, ArrayableContract
{
    use HasMarker;
    use HasNextMarker;
    use NextMarkerSignifiesCompleteness;

    private ?int $maxResults = null;

    private array $containers;

    public function __construct(array $containers, string $nextMarker = "")
    {
        $this->containers = $containers;
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

    /** @return BlobContainerInfo[] */
    public function toArray(): array
    {
        return $this->containers;
    }

    /** @return Traversable<BlobContainerInfo> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->containers);
    }
}
