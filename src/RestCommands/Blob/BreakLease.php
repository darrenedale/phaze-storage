<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Types\LeaseTime;
use Psr\Http\Message\ResponseInterface;

class BreakLease extends AbstractLeaseCommand
{
    protected static function action(): string
    {
        return parent::BreakAction;
    }

    public function parseResponse(ResponseInterface $response): LeaseTime
    {
        if (202 !== $response->getStatusCode()) {
            throw new BlobStorageException("Failed to break lease for container {$this->container()}: {$response->getReasonPhrase()}");
        }

        if (!$response->hasHeader(self::LeaseTimeHeader)) {
            throw new BlobStorageException("Expected response header \"" . self::LeaseTimeHeader . "\" not found");
        }

        $headers = $response->getHeader(self::LeaseTimeHeader);

        if (1 !== count($headers)) {
            throw new BlobStorageException("Expected exactly one \"" . self::LeaseTimeHeader . "\" header in the HTTP response, found " . count($headers));
        }

        $time = filter_var($headers[0], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if (null === $time) {
            throw new BlobStorageException("Expected integer in response header \"" . self::LeaseTimeHeader . "\", found \"{$headers[0]}\"");
        }

        return new LeaseTime($time);
    }
}
