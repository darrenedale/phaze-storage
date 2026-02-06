<?php

declare(strict_types=1);

namespace Phaze\Storage\Responses;

use Phaze\Common\Traits\ImplementsPhpStringableViaPhazeStringable;
use Phaze\Storage\Responses\GetBlobProperties as GetBlobPropertiesResponse;
use Phaze\Storage\Traits\Blob\HasClientRequestId;
use Phaze\Storage\Traits\Blob\HasCommittedBlockCount;
use Phaze\Storage\Traits\Blob\HasContentChecksums;
use Phaze\Storage\Traits\Blob\HasContentDetails;
use Phaze\Storage\Traits\Blob\HasCopyDetails;
use Phaze\Storage\Traits\Blob\HasCreationTime;
use Phaze\Storage\Traits\Blob\HasDate;
use Phaze\Storage\Traits\Blob\HasEncryptionDetails;
use Phaze\Storage\Traits\Blob\HasEtag;
use Phaze\Storage\Traits\Blob\HasImmutabilityPolicyDetails;
use Phaze\Storage\Traits\Blob\HasLastAccessTime;
use Phaze\Storage\Traits\Blob\HasLastModified;
use Phaze\Storage\Traits\Blob\HasLeaseDetails;
use Phaze\Storage\Traits\Blob\HasLegalHold;
use Phaze\Storage\Traits\Blob\HasMetadata;
use Phaze\Storage\Traits\Blob\HasOwnershipAndPermissions;
use Phaze\Storage\Traits\Blob\HasRange;
use Phaze\Storage\Traits\Blob\HasRequestId;
use Phaze\Storage\Traits\Blob\HasResourceType;
use Phaze\Storage\Traits\Blob\HasRestApiVersion;
use Phaze\Storage\Traits\Blob\HasSealed;
use Phaze\Storage\Traits\Blob\HasSequenceNumber;
use Phaze\Storage\Traits\Blob\HasTagCount;
use Phaze\Storage\Types\BlobType;
use Psr\Http\Message\StreamInterface;

class GetBlob extends GetBlobPropertiesResponse
{
    use ImplementsPhpStringableViaPhazeStringable;
    use HasLastModified;
    use HasCreationTime;
    use HasLastAccessTime;
    use HasDate;
    use HasEtag;
    use HasContentDetails;
    use HasContentChecksums;
    use HasMetadata;
    use HasRange;
    use HasSequenceNumber;
    use HasTagCount;
    use HasCopyDetails;
    use HasLeaseDetails;
    use HasRequestId;
    use HasRestApiVersion;
    use HasCommittedBlockCount;
    use HasEncryptionDetails;
    use HasClientRequestId;
    use HasSealed;
    use HasImmutabilityPolicyDetails;
    use HasLegalHold;
    use HasOwnershipAndPermissions;
    use HasResourceType;

    private string|StreamInterface $content;

    public function __construct(BlobType $type, string $name, string|StreamInterface $content)
    {
        parent::__construct($type, $name);
        $this->content = $content;
    }

    public function __destruct()
    {
        $this->scrubCustomerProvidedKeySha256();
    }

    public function content(): string|StreamInterface
    {
        return $this->content;
    }

    public function withContent(string|StreamInterface $content): self
    {
        $clone = clone $this;
        $clone->content = $content;
        return $clone;
    }

    public function toString(): string
    {
        return (string) $this->content();
    }
}
