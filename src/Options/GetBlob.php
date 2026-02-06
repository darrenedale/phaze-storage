<?php

declare(strict_types=1);

namespace Phaze\Storage\Options;

use Phaze\Common\Types\Range;
use Phaze\Storage\Constants;
use Phaze\Storage\Traits\Options\HasCustomerEncryptionOptions;
use Phaze\Storage\Traits\Options\HasLeaseIdOption;
use Phaze\Storage\Traits\Options\HasSnapshotAndVersionOptions;
use Phaze\Storage\Traits\Options\ExposesAllOptions;
use Phaze\Storage\Traits\Options\FiltersOptionsByKey;
use Phaze\Storage\Traits\Options\HasClientRequestIdOption;
use Phaze\Storage\Traits\Options\HasTimeoutOption;

class GetBlob extends AbstractOptions
{
    use HasLeaseIdOption;
    use HasSnapshotAndVersionOptions;
    use HasCustomerEncryptionOptions;
    use HasClientRequestIdOption;
    use HasTimeoutOption;
    use FiltersOptionsByKey;
    use ExposesAllOptions;

    public function __destruct()
    {
        $this->scrubEncryptionKey();
        $this->scrubEncryptionKeySha256();
    }

    public function range(): ?Range
    {
        return $this->option("Range");
    }

    public function withRange(Range $range): self
    {
        $clone = clone $this;
        $clone->setOption("Range", $range);
        return $clone;
    }

    public function withoutRange(): self
    {
        $clone = clone $this;
        $clone->clearOption("Range");
        return $clone;
    }

    public function rangeGetContentMd5(): ?bool
    {
        return $this->option(Constants::HeaderRangeGetContentMd5);
    }

    public function withRangeGetContentMd5(bool $get): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderRangeGetContentMd5, $get);
        return $clone;
    }

    public function withoutRangeGetContentMd5(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderRangeGetContentMd5);
        return $clone;
    }

    public function rangeGetContentCrc64(): ?bool
    {
        return $this->option(Constants::HeaderRangeGetContentCrc64);
    }

    public function withRangeGetContentCrc64(bool $get): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderRangeGetContentCrc64, $get);
        return $clone;
    }

    public function withoutRangeGetContentCrc64(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderRangeGetContentCrc64);
        return $clone;
    }
}
