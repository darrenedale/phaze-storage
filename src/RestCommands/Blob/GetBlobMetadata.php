<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use InvalidArgumentException;
use Phaze\Common\Constants as CommonConstants;
use Phaze\Storage\Constants;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Options\GetBlobMetadata as GetBlobMetadataOptions;
use Phaze\Storage\Responses\GetBlobMetadata as GetBlobMetadataResponse;
use Phaze\Storage\Services\AbstractStorageService;
use Phaze\Storage\Traits\ParsesBlobMetadataFromHeaders;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\BlobName;
use Phaze\Storage\Types\ContainerName;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Psr\Http\Message\ResponseInterface;

use function Phaze\Common\Utilities\DateTime\currentDateTimeForHeader;
use function Phaze\Common\Utilities\Response\readSingleHeader;
use function Phaze\Common\Utilities\String\parseDateTime;

class GetBlobMetadata extends AbstractContainerCommand
{
    use HasNoBody;
    use ParsesBlobMetadataFromHeaders;

    private const UriParameterOptions = ["timeout", "snapshot", "versionid",];

    private const HeaderOptions = [Constants::HeaderLeaseId, Constants::HeaderEncryptionKey, Constants::HeaderEncryptionAlgorithm, Constants::HeaderEncryptionKeySha256,];

    private BlobName $blobName;

    private GetBlobMetadataOptions $options;

    public function __construct(AccountName $account, ContainerName $container, BlobName $blobName, ?GetBlobMetadataOptions $options = null)
    {
        parent::__construct($account, $container);
        $this->blobName = $blobName;
        $this->options = $options ?? new GetBlobMetadataOptions();
    }

    public function blobName(): BlobName
    {
        return $this->blobName;
    }

    public function options(): GetBlobMetadataOptions
    {
        return $this->options;
    }

    public function headers(): array
    {
        // all these options are already strings
        $headers = $this->options()->only(self::HeaderOptions);
        $headers["Date"] = currentDateTimeForHeader();
        $headers["x-ms-version"] = AbstractStorageService::VersionDefault;
        return $headers;
    }

    public function uri(): string
    {
        $query = http_build_query($this->options()->only(self::UriParameterOptions));
        return "https://{$this->account()}.blob.core.windows.net/{$this->container()}/" . urlencode((string) $this->blobName()) . "?comp=metadata" . ("" === $query ? "" : "&{$query}");
    }

    public function method(): string
    {
        return CommonConstants::MethodHead;
    }

    public function parseResponse(ResponseInterface $response): GetBlobMetadataResponse
    {
        if (200 !== $response->getStatusCode()) {
            throw new BlobStorageException("Failed fetching blob metadata: {$response->getReasonPhrase()}");
        }

        try {
            $blob = new GetBlobMetadataResponse();
            $blob = self::parseBlobMetadataHeaders($blob, $response);

            if ($response->hasHeader("ETag")) {
                $blob = $blob->withEtag(readSingleHeader($response, "ETag"));
            }

            if ($response->hasHeader("Date")) {
                $blob = $blob->withDate(parseDateTime(readSingleHeader($response, "Date"), DATE_RFC1123));
            }

            if ($response->hasHeader("Last-Modified")) {
                $blob = $blob->withDate(parseDateTime(readSingleHeader($response, "Last-Modified"), DATE_RFC1123));
            }

            if ($response->hasHeader(Constants::HeaderRequestId)) {
                $blob = $blob->withRequestId(readSingleHeader($response, Constants::HeaderRequestId));
            }

            if ($response->hasHeader(Constants::HeaderVersion)) {
                $blob = $blob->withVersion(readSingleHeader($response, Constants::HeaderVersion));
            }

            if ($response->hasHeader(Constants::HeaderClientRequestId)) {
                $blob = $blob->withClientRequestId(readSingleHeader($response, Constants::HeaderClientRequestId));
            }
        } catch (InvalidArgumentException $err) {
            throw new BlobStorageException("Exception parsing header value: {$err->getMessage()}", previous: $err);
        }

        return $blob;
    }
}
