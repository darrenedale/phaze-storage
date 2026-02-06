<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Common\Types\Guid;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\ContainerName;
use Phaze\Storage\Types\LeaseDuration;
use Psr\Http\Message\ResponseInterface;

class AcquireLease extends AbstractLeaseCommand
{
    private LeaseDuration $duration;

    private ?Guid $proposedId;

    public function __construct(AccountName $account, ContainerName $container, LeaseDuration $duration, ?Guid $proposedId = null)
    {
        parent::__construct($account, $container);
        $this->duration = $duration;
        $this->proposedId = $proposedId;
    }

    public function duration(): LeaseDuration
    {
        return $this->duration;
    }

    public function proposedLeaseId(): ?Guid
    {
        return $this->proposedId;
    }

    protected static function action(): string
    {
        return parent::AcquireAction;
    }

    public function headers(): array
    {
        $headers = parent::headers();
        $headers[self::LeaseDurationHeader] = (string) $this->duration();

        if (null !== $this->proposedLeaseId()) {
            $headers[self::ProposedLeaseIdHeader] = (string) $this->proposedLeaseId();
        }

        return $headers;
    }

    public function parseResponse(ResponseInterface $response): Guid
    {
        if (201 !== $response->getStatusCode()) {
            throw new BlobStorageException("Failed to acquire lease on container {$this->container()}: {$response->getReasonPhrase()}");
        }

        if (!$response->hasHeader(self::LeaseIdHeader)) {
            throw new BlobStorageException("Expecting x-ms-lease-id response header, none found");
        }

        $leaseId = $response->getHeader(self::LeaseIdHeader);

        if (1 !== count($leaseId)) {
            throw new BlobStorageException("Expecting exactly one x-ms-lease-id response header, found " . count($leaseId));
        }

        return new Guid(trim($leaseId[0]));
    }
}