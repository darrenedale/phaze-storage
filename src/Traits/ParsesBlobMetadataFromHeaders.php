<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits;

use Phaze\Storage\Constants;
use Phaze\Storage\Contracts\BlobMetadata as BlobMetadataContract;
use Psr\Http\Message\ResponseInterface;

trait ParsesBlobMetadataFromHeaders
{
    /**
     * @template T of BlobMetadataContract
     * @param T $blob
     * @param ResponseInterface $response
     * @return T
     */
    static protected function parseBlobMetadataHeaders(BlobMetadataContract $blob, ResponseInterface $response): BlobMetadataContract
    {
        $metaHeaders = array_filter(
            $response->getHeaders(),
            fn(string $key): bool => str_starts_with($key, Constants::HeaderPrefixMeta),
            ARRAY_FILTER_USE_KEY
        );

        foreach ($metaHeaders as $header => $values) {
            $key = substr($header, strlen(Constants::HeaderPrefixMeta));
            $blob = $blob->withMetadata($key, $values[0]);
        }

        return $blob;
    }
}
