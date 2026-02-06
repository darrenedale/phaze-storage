<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use InvalidArgumentException;
use Phaze\Common\Constants as CommonConstants;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Phaze\Storage\Constants;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Options\GetBlobProperties as GetBlobPropertiesOptions;
use Phaze\Storage\Responses\GetBlobProperties as GetBlobPropertiesResponse;
use Phaze\Storage\Services\AbstractStorageService;
use Phaze\Storage\Traits\ParsesBlobMetadataFromHeaders;
use Phaze\Storage\Traits\ParsesBlobPropertiesFromHeaders;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\BlobName;
use Phaze\Storage\Types\BlobType;
use Phaze\Storage\Types\ContainerName;
use Psr\Http\Message\ResponseInterface;

use function Phaze\Common\Utilities\DateTime\currentDateTimeForHeader;
use function Phaze\Common\Utilities\Response\readSingleHeader;

class GetBlobProperties extends AbstractContainerCommand
{
    use HasNoBody;
    use ParsesBlobPropertiesFromHeaders;
    use ParsesBlobMetadataFromHeaders;

    private const UriParameterOptions = ["timeout", "snapshot", "versionid",];

    private const HeaderOptions = [Constants::HeaderEncryptionKey, Constants::HeaderEncryptionAlgorithm, Constants::HeaderEncryptionKeySha256,];

    private BlobName $blobName;

    private GetBlobPropertiesOptions $options;

    public function __construct(AccountName $account, ContainerName $container, BlobName $blobName, ?GetBlobPropertiesOptions $options = null)
    {
        parent::__construct($account, $container);
        $this->blobName = $blobName;
        $this->options = $options ?? new GetBlobPropertiesOptions();
    }

    public function blobName(): BlobName
    {
        return $this->blobName;
    }

    public function options(): GetBlobPropertiesOptions
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
        return "https://{$this->account()}.blob.core.windows.net/{$this->container()}/" . urlencode((string) $this->blobName()) . ("" === $query ? "" : "?{$query}");
    }

    public function method(): string
    {
        return CommonConstants::MethodHead;
    }

    public function parseResponse(ResponseInterface $response): GetBlobPropertiesResponse
    {
        if (200 !== $response->getStatusCode()) {
            throw new BlobStorageException("Failed fetching blob properties: {$response->getReasonPhrase()}");
        }

//        echo "Response headers:\n\n";
//
//        foreach ($response->getHeaders() as $header => $values) {
//            foreach ($values as $value)
//                echo "{$header}: {$value}\n";
//        }
//
//        echo "\n";
//
        try {
            $blob = (new GetBlobPropertiesResponse(new BlobType(readSingleHeader($response, Constants::HeaderBlobType)), (string) $this->blobName()));
            $blob = self::parseBlobPropertyHeaders($blob, $response);
            $blob = self::parseBlobMetadataHeaders($blob, $response);

            if ($response->hasHeader(Constants::HeaderClientRequestId)) {
                $blob = $blob->withClientRequestId(readSingleHeader($response, Constants::HeaderClientRequestId));
            }

        } catch (InvalidArgumentException $err) {
            throw new BlobStorageException("Exception parsing header value: {$err->getMessage()}", previous: $err);
        }

        return $blob;
    }
}
