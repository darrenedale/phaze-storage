<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use InvalidArgumentException;
use Phaze\Common\Constants as CommonConstants;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Phaze\Common\Types\Range;
use Phaze\Storage\Constants;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Options\GetBlob as GetBlobOptions;
use Phaze\Storage\Responses\GetBlob as GetBlobContentResponse;
use Phaze\Storage\Services\AbstractStorageService;
use Phaze\Storage\Traits\ParsesBlobMetadataFromHeaders;
use Phaze\Storage\Traits\ParsesBlobPropertiesFromHeaders;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\BlobName;
use Phaze\Storage\Types\BlobType;
use Phaze\Storage\Types\ContainerName;
use Psr\Http\Message\ResponseInterface;

use function Phaze\Common\Utilities\Casts\toString;
use function Phaze\Common\Utilities\DateTime\currentDateTimeForHeader;
use function Phaze\Common\Utilities\Iterable\map;
use function Phaze\Common\Utilities\Iterable\toArray;
use function Phaze\Common\Utilities\Response\readSingleHeader;

class GetBlob extends AbstractContainerCommand
{
    use HasNoBody;
    use ParsesBlobPropertiesFromHeaders;
    use ParsesBlobMetadataFromHeaders;

    private const UriParameterOptions = ["timeout", "snapshot", "versionid",];

    /** Any content up to 50MB is returned as a binary string; otherwise a StreamInterface is returned. */
    private const CacheContentThreshold = 1024 * 1024 * 50;

    private BlobName $blobName;

    private GetBlobOptions $options;

    public function __construct(AccountName $account, ContainerName $container, BlobName $blobName, ?GetBlobOptions $options = null)
    {
        parent::__construct($account, $container);
        $this->blobName = $blobName;
        $this->options = $options ?? new GetBlobOptions();
    }

    public function blobName(): BlobName
    {
        return $this->blobName;
    }

    public function options(): GetBlobOptions
    {
        return $this->options;
    }

    public function headers(): array
    {
        // all options are destined for headers and are Stringable, except:
        // - timeout, versionid and snapshot are URL parameters
        $headers = toArray(map($this->options()->except(self::UriParameterOptions), fn (mixed & $value) => $value = toString($value)));
        $headers["Date"] = currentDateTimeForHeader();
        $headers["x-ms-version"] = AbstractStorageService::VersionDefault;
        return $headers;
    }

    public function uri(): string
    {
        $query = http_build_query($this->options()->only(self::UriParameterOptions));
        return "https://{$this->account()}.blob.core.windows.net/{$this->container()}/" . urlencode((string) $this->blobName()) . ("" === $query ? "" : "?{$query}");
    }

    public function method(): string
    {
        return CommonConstants::MethodGet;
    }

    public function parseResponse(ResponseInterface $response): GetBlobContentResponse
    {
        if (
            (null !== $this->options()->range() && 206 !== $response->getStatusCode())
            || (null === $this->options()->range() && 200 !== $response->getStatusCode())
        ) {
            throw new BlobStorageException("Failed fetching blob content: {$response->getReasonPhrase()}");
        }

        $body = $response->getBody();

        if (null !== $body->getSize() && self::CacheContentThreshold >= $body->getSize()) {
            $body = (string) $body;
        }

        try {
            $blob = (new GetBlobContentResponse(new BlobType(readSingleHeader($response, Constants::HeaderBlobType)), (string) $this->blobName(), $body));
            $blob = self::parseBlobPropertyHeaders($blob, $response);
            $blob = self::parseBlobMetadataHeaders($blob, $response);

            if ($response->hasHeader(Constants::HeaderClientRequestId)) {
                $blob = $blob->withClientRequestId(readSingleHeader($response, Constants::HeaderClientRequestId));
            }

            // TODO parse other headers

            if (null !== $this->options()->range()) {
                $blob = $blob->withRange(Range::parse(readSingleHeader($response, "Content-Range")));
            }
        } catch (InvalidArgumentException $err) {
            throw new BlobStorageException("Exception parsing header value: {$err->getMessage()}", previous: $err);
        }

        return $blob;
    }
}
