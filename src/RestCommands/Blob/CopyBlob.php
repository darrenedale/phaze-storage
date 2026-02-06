<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use DateTimeInterface;
use Phaze\Common\Constants as CommonConstants;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Phaze\Storage\Constants;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Options\CopyBlob as CopyBlobOptions;
use Phaze\Storage\Responses\CopyBlob as CopyBlobResponse;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\BlobName;
use Phaze\Storage\Types\ContainerName;
use Phaze\Storage\Types\CopyStatus;
use Phaze\Common\Types\Url;
use Psr\Http\Message\ResponseInterface;

use function Phaze\Common\Utilities\Iterable\map;
use function Phaze\Common\Utilities\Iterable\toArray;
use function Phaze\Common\Utilities\Response\readSingleHeader;
use function Phaze\Common\Utilities\String\parseDateTime;

class CopyBlob extends AbstractContainerCommand
{
    use HasNoBody;

    private const HeaderOptions = [
        Constants::HeaderLeaseId,
        Constants::HeaderSourceLeaseId,
        Constants::HeaderClientRequestId,
        Constants::HeaderAccessTier,
        Constants::HeaderLegalHold,
        Constants::HeaderTags,
        Constants::HeaderSealBlob,
        Constants::HeaderRehydratePolicy,
        Constants::HeaderImmutabilityPolicyMode,
    ];

    private BlobName $blobName;

    private Url|string $source;

    private CopyBlobOptions $options;

    public function __construct(AccountName $account, ContainerName $container, BlobName $blob, Url|string $source, ?CopyBlobOptions $options = null)
    {
        parent::__construct($account, $container);
        $this->blobName = $blob;
        $this->source = $source;
        $this->options = $options ?? new CopyBlobOptions();
    }

    public function blobName(): BlobName
    {
        return $this->blobName;
    }

    public function source(): Url|string
    {
        return $this->source;
    }

    public function options(): CopyBlobOptions
    {
        return $this->options;
    }

    public function headers(): array
    {
        $headers =
            array_merge(
                toArray(map($this->options()->only(self::HeaderOptions), fn (mixed $value): string => (string) $value)),
                array_filter($this->options()->all(), fn (string $key): bool => str_starts_with(Constants::HeaderPrefixMeta, $key), ARRAY_FILTER_USE_KEY)
            );

        $untilDate = $this->options->immutabilityPolicyUntilDate();

        if ($untilDate instanceof DateTimeInterface) {
            // this header is formatted as RFC 1123 (see https://learn.microsoft.com/en-us/rest/api/storageservices/copy-blob)
            $headers[Constants::HeaderImmutabilityPolicyUntilDate] = $untilDate->format(DATE_RFC1123);
        }

        $headers[Constants::HeaderCopySource] = ($this->source() instanceof Url ? urlencode((string) $this->source()) : $this->source());
        return $headers;
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

    public function parseResponse(ResponseInterface $response): CopyBlobResponse
    {
        if ($response->getStatusCode() !== 202) {
            throw new BlobStorageException("Failed to copy blob: {$response->getStatusCode()} {$response->getReasonPhrase()}");
        }

        $copyInfo = new CopyBlobResponse(readSingleHeader($response, Constants::HeaderCopyId));

        if ($response->hasHeader(Constants::HeaderCopyStatus)) {
            $copyInfo = $copyInfo->withCopyStatus(new CopyStatus(readSingleHeader($response, Constants::HeaderCopyStatus)));
        }

        if ($response->hasHeader("ETag")) {
            $copyInfo = $copyInfo->withEtag(readSingleHeader($response, "ETag"));
        }

        if ($response->hasHeader(Constants::HeaderVersion)) {
            $copyInfo = $copyInfo->withVersion(readSingleHeader($response, Constants::HeaderVersion));
        }

        if ($response->hasHeader(Constants::HeaderRequestId)) {
            $copyInfo = $copyInfo->withRequestId(readSingleHeader($response, Constants::HeaderRequestId));
        }

        if ($response->hasHeader("Last-Modified")) {
            $copyInfo = $copyInfo->withLastModified(parseDateTime(readSingleHeader($response, "Last-Modified")));
        }

        if ($response->hasHeader(Constants::HeaderVersionId)) {
            $copyInfo = $copyInfo->withVersionId(readSingleHeader($response, Constants::HeaderVersionId));
        }

        if ($response->hasHeader(Constants::HeaderClientRequestId)) {
            $copyInfo = $copyInfo->withClientRequestId(readSingleHeader($response, Constants::HeaderClientRequestId));
        }

        return $copyInfo;
    }
}