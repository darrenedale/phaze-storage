<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Common\Types\Guid;

// TODO consider moving to Storage\Traits ?
trait HasLeaseIdHeader
{
    abstract public function leaseId(): Guid;

    public function headers(): array
    {
        return array_merge(parent::headers(), [self::LeaseIdHeader => (string) $this->leaseId(),]);
    }
}
