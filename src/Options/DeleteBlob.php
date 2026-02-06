<?php

declare(strict_types=1);

namespace Phaze\Storage\Options;

use InvalidArgumentException;
use Phaze\Storage\Constants;
use Phaze\Storage\Traits\Options\HasLeaseIdOption;
use Phaze\Storage\Traits\Options\HasSnapshotAndVersionOptions;
use Phaze\Storage\Types\DeleteSnapshots;
use Phaze\Storage\Types\DeleteType;
use Phaze\Storage\Traits\Options\ExposesAllOptions;
use Phaze\Storage\Traits\Options\FiltersOptionsByKey;
use Phaze\Storage\Traits\Options\HasClientRequestIdOption;
use Phaze\Storage\Traits\Options\HasTimeoutOption;

class DeleteBlob extends AbstractOptions
{
    use HasLeaseIdOption;
    use HasSnapshotAndVersionOptions;
    use HasClientRequestIdOption;
    use HasTimeoutOption;
    use FiltersOptionsByKey;
    use ExposesAllOptions;

    public function deleteType(): ?DeleteType
    {
        return $this->option("deletetype");
    }

    public function withDeleteType(DeleteType $type): self
    {
        // only permanent is currently supported
        if ($type->type() !== DeleteType::Permanent) {
            throw new InvalidArgumentException("Expected DeleteType \"permanent\", found {$type}");
        }

        $clone = clone $this;
        $clone->setOption("deletetype", $type);
        return $clone;
    }

    public function withoutDeleteType(): self
    {
        $clone = clone $this;
        $clone->clearOption("deletetype");
        return $clone;
    }

    public function deleteSnapshots(): ?DeleteSnapshots
    {
        return $this->option(Constants::HeaderDeleteSnapshots);
    }

    public function withDeleteSnapshots(DeleteSnapshots $delete): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderDeleteSnapshots, $delete);
        return $clone;
    }

    public function withoutDeleteSnapshots(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderDeleteSnapshots);
        return $clone;
    }
}
