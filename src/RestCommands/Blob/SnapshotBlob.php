<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use InvalidArgumentException;
use Phaze\Common\Constants as CommonConstants;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Storage\Constants;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Options\SnapshotBlob as SnapshotBlobOptions;
use Phaze\Storage\Responses\SnapshotBlob as SnapshotBlobResponse;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\BlobName;
use Phaze\Storage\Types\ContainerName;
use Phaze\Storage\Types\EncryptionScope;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Psr\Http\Message\ResponseInterface;

use function Phaze\Common\Utilities\Iterable\map;
use function Phaze\Common\Utilities\Iterable\toArray;
use function Phaze\Common\Utilities\Response\readSingleHeader;
use function Phaze\Common\Utilities\String\parseDateTime;

class SnapshotBlob extends AbstractContainerCommand
{
    use HasNoBody;

    private const HeaderOptions = [
        Constants::HeaderLeaseId,
        Constants::HeaderEncryptionKey,
        Constants::HeaderEncryptionKeySha256,
        Constants::HeaderEncryptionAlgorithm,
    ];

    private BlobName $blobName;

    private SnapshotBlobOptions $options;

    public function __construct(AccountName $account, ContainerName $container, BlobName $blob, ?SnapshotBlobOptions $options = null)
    {
        parent::__construct($account, $container);
        $this->blobName = $blob;
        $this->options = $options ?? new SnapshotBlobOptions();
    }

    public function blobName(): BlobName
    {
        return $this->blobName;
    }

    public function options(): SnapshotBlobOptions
    {
        return $this->options;
    }

    public function headers(): array
    {
        return
            array_merge(
                toArray(map($this->options()->only(self::HeaderOptions), fn (mixed $value): string => (string) $value)),
                array_filter($this->options()->all(), fn (string $key): bool => str_starts_with(Constants::HeaderPrefixMeta, $key), ARRAY_FILTER_USE_KEY)
            );
    }

    public function uri(): string
    {
        $queryArgs = $this->options()->only(["timeout"]);
        $queryArgs["comp"] = "snapshot";
        return "https://{$this->account()}.blob.core.windows.net/{$this->container()}/{$this->blobName()}?" . http_build_query($queryArgs);
    }

    public function method(): string
    {
        return CommonConstants::MethodPut;
    }

    public function parseResponse(ResponseInterface $response): SnapshotBlobResponse
    {
        if ($response->getStatusCode() !== 201) {
            throw new BlobStorageException("Failed to snapshot blob: {$response->getStatusCode()} {$response->getReasonPhrase()}");
        }

        $snapshot = new SnapshotBlobResponse(readSingleHeader($response, Constants::HeaderSnapshot));

        if ($response->hasHeader("Etag")) {
            $snapshot = $snapshot->withEtag(readSingleHeader($response, "Etag"));
        }

        if ($response->hasHeader("Date")) {
            $snapshot = $snapshot->withDate(parseDateTime(readSingleHeader($response, "Date")));
        }

        if ($response->hasHeader(Constants::HeaderVersion)) {
            $snapshot = $snapshot->withVersion(readSingleHeader($response, Constants::HeaderVersion));
        }

        if ($response->hasHeader(Constants::HeaderRequestId)) {
            $snapshot = $snapshot->withRequestId(readSingleHeader($response, Constants::HeaderRequestId));
        }

        if ($response->hasHeader(Constants::HeaderClientRequestId)) {
            $snapshot = $snapshot->withClientRequestId(readSingleHeader($response, Constants::HeaderClientRequestId));
        }

        if ($response->hasHeader(Constants::HeaderVersionId)) {
            $snapshot = $snapshot->withVersionId(readSingleHeader($response, Constants::HeaderVersionId));
        }

        if ($response->hasHeader("Last-Modified")) {
            try {
                $snapshot = $snapshot->withLastModified(parseDateTime(readSingleHeader($response, "Last-Modified")));
            } catch (InvalidArgumentException $err) {
                throw new BlobStorageException("Expected valid date-time in Last-Modified response header, found \"" . readSingleHeader($response, "Last-Modified") . "\"", previous: $err);
            }
        }

        if ($response->hasHeader(Constants::HeaderEncryptionScope)) {
            try {
                $snapshot = $snapshot->withEncryptionScope(new EncryptionScope(readSingleHeader($response, Constants::HeaderEncryptionScope)));
            } catch (InvalidValueException $err) {
                throw new BlobStorageException("Expected valid encruption scope in " . Constants::HeaderEncryptionScope . " response header, found \"" . readSingleHeader($response, Constants::HeaderEncryptionScope) . "\"", previous: $err);
            }
        }

        if ($response->hasHeader(Constants::HeaderEncryptionKeySha256)) {
            $snapshot = $snapshot->withEncryptionKeySha256(readSingleHeader($response, Constants::HeaderEncryptionScope));
        }

        return $snapshot;
    }
}