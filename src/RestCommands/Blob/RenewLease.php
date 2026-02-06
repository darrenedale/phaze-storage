<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Common\Traits\RestCommands\ResponseHasNoBody;
use Phaze\Common\Types\Guid;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\ContainerName;
use Psr\Http\Message\ResponseInterface;

class RenewLease extends AbstractLeaseCommand
{
    use HasLeaseIdHeader;
    use ResponseHasNoBody;

    private Guid $leaseId;

    public function __construct(AccountName $account, ContainerName $container, Guid $leaseId)
    {
        parent::__construct($account, $container);
        $this->leaseId = $leaseId;
    }

    public function leaseId(): Guid
    {
        return $this->leaseId;
    }

    protected static function action(): string
    {
        return parent::RenewAction;
    }

    private function createErrorException(ResponseInterface $response): BlobStorageException
    {
        return new BlobStorageException("Failed to renew lease {$this->leaseId()} for container {$this->container()}: {$response->getReasonPhrase()}");
    }
}