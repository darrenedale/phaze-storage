<?php

declare(strict_types=1);

namespace Phaze\Storage\Responses;

use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Storage\Traits\Blob\HasClientRequestId;
use Phaze\Storage\Traits\Blob\HasDate;
use Phaze\Storage\Traits\Blob\HasEncryptionKeySha256;
use Phaze\Storage\Traits\Blob\HasEncryptionScope;
use Phaze\Storage\Traits\Blob\HasEtag;
use Phaze\Storage\Traits\Blob\HasLastModified;
use Phaze\Storage\Traits\Blob\HasRequestId;
use Phaze\Storage\Traits\Blob\HasRestApiVersion;
use Phaze\Storage\Traits\Blob\HasSnapshot;
use Phaze\Storage\Traits\Blob\HasVersionId;
use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Stringable;

class SnapshotBlob implements StringableContract, Stringable
{
    use HasRequestId;
    use HasRestApiVersion;
    use HasDate;
    use HasSnapshot;
    use HasVersionId;
    use HasEtag;
    use HasLastModified;
    use HasEncryptionScope;
    use HasEncryptionKeySha256;
    use HasClientRequestId;
    use ImplementsPhpStringableViaPhazeStringable;

    public function __construct(string $snapshot)
    {
        $this->snapshot = $snapshot;
    }

    public function __destruct()
    {
        $this->scrubEncryptionKeySha256();
    }

    public function toString(): string
    {
        return $this->snapshot();
    }
}
