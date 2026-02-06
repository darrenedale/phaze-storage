<?php

declare(strict_types=1);

namespace Phaze\Storage\Options;

use Phaze\Storage\Constants;
use Phaze\Storage\Traits\Options\HasAccessTierOption;
use Phaze\Storage\Traits\Options\HasCustomerEncryptionOptions;
use Phaze\Storage\Traits\Options\HasEncryptionScopeOption;
use Phaze\Storage\Traits\Options\HasLegalHoldOption;
use Phaze\Storage\Traits\Options\HasTagsOption;
use Phaze\Storage\Types\BlobType;
use Phaze\Storage\Traits\Options\ExposesAllOptions;
use Phaze\Storage\Traits\Options\FiltersOptionsByKey;
use Phaze\Storage\Traits\Options\HasClientRequestIdOption;
use Phaze\Storage\Traits\Options\HasTimeoutOption;

class PutBlob extends AbstractOptions
{
    use HasAccessTierOption;
    use HasClientRequestIdOption;
    use HasEncryptionScopeOption;
    use HasLegalHoldOption;
    use HasTagsOption;
    use HasTimeoutOption;
    use HasCustomerEncryptionOptions;
    use FiltersOptionsByKey;
    use ExposesAllOptions;

    public function __construct(?BlobType $type = null)
    {
        $this->setOption(Constants::HeaderBlobType, $type ?? new BlobType(BlobType::BlockBlob));
    }

    public function __destruct()
    {
        $this->scrubEncryptionKey();
        $this->scrubEncryptionKeySha256();
    }

    public function blobType(): BlobType
    {
        return $this->option(Constants::HeaderBlobType);
    }

    public function withBlobType(BlobType $type): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderBlobType, $type);
        return $clone;
    }

    public function contentMd5(): ?string
    {
        return $this->option("Content-MD5");
    }

    public function withContentMd5(string $md5): self
    {
        $clone = clone $this;
        $clone->setOption("Content-MD5", $md5);
        return $clone;
    }

    public function withoutContentMd5(): self
    {
        $clone = clone $this;
        $clone->clearOption("Content-MD5");
        return $clone;
    }

    public function contentCrc64(): ?string
    {
        return $this->option(Constants::HeaderContentCrc64);
    }

    public function withContentCrc64(string $crc64): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderContentCrc64, $crc64);
        return $clone;
    }

    public function withoutContentCrc64(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderContentCrc64);
        return $clone;
    }
}
