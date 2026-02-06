<?php

declare(strict_types=1);

namespace Phaze\Storage\Responses;

use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Phaze\Storage\Contracts\BlobProperties as BlobPropertiesContract;
use Phaze\Common\Contracts\Types\Stringable as StringableContract;
use Phaze\Storage\Traits\Blob\HasClientRequestId;
use Phaze\Storage\Traits\Blob\HasCommittedBlockCount;
use Phaze\Storage\Traits\Blob\HasContentChecksums;
use Phaze\Storage\Traits\Blob\HasContentDetails;
use Phaze\Storage\Traits\Blob\HasCopyDetails;
use Phaze\Storage\Traits\Blob\HasCurrentVersionFlag;
use Phaze\Storage\Traits\Blob\HasEncryptionDetails;
use Phaze\Storage\Traits\Blob\HasEtag;
use Phaze\Storage\Traits\Blob\HasImmutabilityPolicyDetails;
use Phaze\Storage\Traits\Blob\HasLeaseDetails;
use Phaze\Storage\Traits\Blob\HasLegalHold;
use Phaze\Storage\Traits\Blob\HasMetadata;
use Phaze\Storage\Traits\Blob\HasName;
use Phaze\Storage\Traits\Blob\HasOwnershipAndPermissions;
use Phaze\Storage\Traits\Blob\HasResourceType;
use Phaze\Storage\Traits\Blob\HasSealed;
use Phaze\Storage\Traits\Blob\HasSequenceNumber;
use Phaze\Storage\Traits\Blob\HasSnapshot;
use Phaze\Storage\Traits\Blob\HasTagCount;
use Phaze\Storage\Traits\Blob\HasTimestamps;
use Phaze\Storage\Traits\Blob\HasVersionId;
use Phaze\Storage\Types\BlobType;
use Stringable;

class GetBlobProperties implements Stringable, StringableContract, BlobPropertiesContract
{
    use ImplementsPhpStringableViaPhazeStringable;
    use HasName;
    use HasVersionId;
    use HasCurrentVersionFlag;
    use HasSnapshot;
    use HasSequenceNumber;
    use HasEtag;
    use HasContentDetails;
    use HasContentChecksums;
    use HasTimestamps;
    use HasOwnershipAndPermissions;
    use HasResourceType;
    use HasEncryptionDetails;
    use HasLeaseDetails;
    use HasCopyDetails;
    use HasLegalHold;
    use HasMetadata;
    use HasImmutabilityPolicyDetails;
    use HasClientRequestId;
    use HasSealed;
    use HasTagCount;
    use HasCommittedBlockCount;

    private BlobType $type;

    // MD5 of the full blob (differs from above if the request had a Range header representing of subset of the blob)
    private string $fullBlobMd5 = "";

    public function __construct(BlobType $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;
    }

    public function __destruct()
    {
        $this->scrubCustomerProvidedKeySha256();
    }

    public function type(): BlobType
    {
        return $this->type;
    }

    public function withType(BlobType $type): self
    {
        $clone = clone $this;
        $clone->type = $type;
        return $clone;
    }

    public function fullBlobMd5(): string
    {
        return $this->fullBlobMd5;
    }

    public function withFullBlobMd5(string $md5): self
    {
        $clone = clone $this;
        $clone->fullBlobMd5 = $md5;
        return $clone;
    }

    public function toString(): string
    {
        return $this->name();
    }
}
