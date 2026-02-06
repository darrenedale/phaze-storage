<?php

declare(strict_types=1);

namespace Phaze\Storage\Responses;

use Phaze\Common\Types\Guid;
use Phaze\Storage\Traits\Blob\HasClientRequestId;
use Phaze\Storage\Traits\Blob\HasDate;
use Phaze\Storage\Traits\Blob\HasEtag;
use Phaze\Storage\Traits\Blob\HasLastModified;
use Phaze\Storage\Traits\Blob\HasRequestId;
use Phaze\Storage\Traits\Blob\HasRestApiVersion;
use Phaze\Storage\Types\LeaseTime;

class LeaseBlob
{
    use HasRestApiVersion;
    use HasDate;
    use HasEtag;
    use HasLastModified;
    use HasRequestId;
    use HasClientRequestId;

    private Guid $id;

    private ?LeaseTime $time;

    public function __construct(Guid $id, ?LeaseTime $time = null)
    {
        $this->id = $id;
        $this->time = $time;
    }

    public function id(): Guid
    {
        return $this->id;
    }

    public function withId(Guid $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function time(): ?LeaseTime
    {
        return $this->time;
    }

    public function withTime(?LeaseTime $time): self
    {
        $clone = clone $this;
        $clone->time = $time;
        return $clone;
    }
}
