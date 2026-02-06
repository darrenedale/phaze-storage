<?php

declare(strict_types=1);

namespace Phaze\Storage\Responses;

use ArrayIterator;
use IteratorAggregate;
use Phaze\Storage\Contracts\BlobMetadata as BlobMetadataContract;
use Phaze\Storage\Traits\Blob\HasClientRequestId;
use Phaze\Storage\Traits\Blob\HasDate;
use Phaze\Storage\Traits\Blob\HasEtag;
use Phaze\Storage\Traits\Blob\HasLastModified;
use Phaze\Storage\Traits\Blob\HasMetadata;
use Phaze\Storage\Traits\Blob\HasRequestId;
use Phaze\Storage\Traits\Blob\HasRestApiVersion;
use Traversable;

class GetBlobMetadata implements BlobMetadataContract, IteratorAggregate
{
    use HasEtag;
    use HasLastModified;
    use HasDate;
    use HasRequestId;
    use HasRestApiVersion;
    use HasMetadata;
    use HasClientRequestId;

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->metadata());
    }
}
