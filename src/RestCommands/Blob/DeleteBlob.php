<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use InvalidArgumentException;
use Phaze\Common\Constants as CommonConstants;
use Phaze\Storage\Constants;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Options\DeleteBlob as DeleteBlobOptions;
use Phaze\Storage\Services\AbstractStorageService;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\BlobName;
use Phaze\Storage\Types\ContainerName;
use Phaze\Storage\Types\DeleteType;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Psr\Http\Message\ResponseInterface;

use function Phaze\Common\Utilities\DateTime\currentDateTimeForHeader;
use function Phaze\Common\Utilities\Iterable\map;
use function Phaze\Common\Utilities\Iterable\toArray;
use function Phaze\Common\Utilities\Response\readSingleHeader;
use function Phaze\Common\Utilities\String\parseBoolean;
use function Phaze\Common\Utilities\Casts\toString;

class DeleteBlob extends AbstractContainerCommand
{
    use HasNoBody;

    private const UriParameterOptions = ["timeout", "snapshot", "versionid", "deletetype",];

    private const HeaderOptions = [Constants::HeaderLeaseId, Constants::HeaderDeleteSnapshots, Constants::HeaderClientRequestId,];

    /** Any content up to 50MB is returned as a binary string; otherwise a StreamInterface is returned. */
    private const CacheContentThreshold = 1024 * 1024 * 50;

    private BlobName $blobName;

    private DeleteBlobOptions $options;

    public function __construct(AccountName $account, ContainerName $container, BlobName $blobName, ?DeleteBlobOptions $options = null)
    {
        parent::__construct($account, $container);
        $this->blobName = $blobName;
        $this->options = $options ?? new DeleteBlobOptions();
    }

    public function blobName(): BlobName
    {
        return $this->blobName;
    }

    public function options(): DeleteBlobOptions
    {
        return $this->options;
    }

    public function headers(): array
    {
        // all options are destined for headers and are Stringable, except:
        // - timeout, versionid and snapshot are URL parameters
        $headers = toArray(map($this->options()->only(self::HeaderOptions), fn (mixed $value): string => toString($value)));
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
        return CommonConstants::MethodDelete;
    }

    public function parseResponse(ResponseInterface $response): DeleteType
    {
        if (202 !== $response->getStatusCode()) {
            throw new BlobStorageException("Failed deleting blob: {$response->getReasonPhrase()}");
        }

        echo "Response headers:\n\n";

        foreach ($response->getHeaders() as $header => $values) {
            foreach ($values as $value)
                echo "{$header}: {$value}\n";
        }

        echo "\n";

        $permanent = false;

        if ($response->hasHeader(Constants::HeaderDeleteTypePermanent)) {
            try {
                $permanent = parseBoolean(readSingleHeader($response, Constants::HeaderDeleteTypePermanent));
            } catch (InvalidArgumentException $err) {
                throw new BlobStorageException("Exception parsing " . Constants::HeaderDeleteTypePermanent . " response header: {$err->getMessage()}", previous: $err);
            }
        }

        return new DeleteType($permanent ? DeleteType::Permanent : DeleteType::Soft);
    }
}
