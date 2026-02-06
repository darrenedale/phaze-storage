<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Common\Traits\RestCommands\ResponseHasNoBody;
use Phaze\Common\Types\Guid;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\ContainerName;
use Psr\Http\Message\ResponseInterface;

class ChangeLease extends AbstractLeaseCommand
{
    use HasLeaseIdHeader {
        headers as hasLeaseIdTraitHeaders;
    }

    use ResponseHasNoBody;

    private Guid $leaseId;

    private ?Guid $proposedId;

    public function __construct(AccountName $account, ContainerName $container, Guid $leaseId, Guid $proposedId)
    {
        parent::__construct($account, $container);
        $this->leaseId = $leaseId;
        $this->proposedId = $proposedId;
    }

    public function leaseId(): Guid
    {
        return $this->leaseId;
    }

    public function proposedLeaseId(): Guid
    {
        return $this->proposedId;
    }

    protected static function action(): string
    {
        return parent::ChangeAction;
    }

    public function headers(): array
    {
        $headers = $this->hasLeaseIdTraitHeaders();
        $headers[self::ProposedLeaseIdHeader] = (string) $this->proposedLeaseId();
        return $headers;
    }

    private function createErrorException(ResponseInterface $response): BlobStorageException
    {
        return new BlobStorageException("Failed to change lease {$this->leaseId()} on container {$this->container()} to {$this->proposedLeaseId()}: {$response->getReasonPhrase()}");
    }
}
