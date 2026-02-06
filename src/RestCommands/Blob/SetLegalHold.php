<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Common\Constants as CommonConstants;
use Phaze\Storage\Constants;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Options\SetLegalHoldOptions;
use Phaze\Storage\Responses\SetLegalHold as SetLegalHoldResponse;
use Phaze\Storage\Services\AbstractStorageService;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\BlobName;
use Phaze\Storage\Types\ContainerName;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Psr\Http\Message\ResponseInterface;

use function Phaze\Common\Utilities\DateTime\currentDateTimeForHeader;
use function Phaze\Common\Utilities\Iterable\map;
use function Phaze\Common\Utilities\Iterable\toArray;
use function Phaze\Common\Utilities\Casts\toString;
use function Phaze\Common\Utilities\Response\readSingleHeader;
use function Phaze\Common\Utilities\String\parseBoolean;

class SetLegalHold extends AbstractContainerCommand
{
    private const HeaderOptions = [Constants::HeaderLegalHold, Constants::HeaderClientRequestId,];

    private const UriParameterOptions = ["timeout", "snapshot", "version",];

    use HasNoBody;

    private BlobName $blobName;

    private SetLegalHoldOptions $options;

    public function __construct(AccountName $account, ContainerName $container, BlobName $blobName, SetLegalHoldOptions $options)
    {
        parent::__construct($account, $container);
        $this->blobName = $blobName;
        $this->options = $options;
    }

    public function blobName(): BlobName
    {
        return $this->blobName;
    }

    public function options(): SetLegalHoldOptions
    {
        return $this->options;
    }

    public function headers(): array
    {
        $headers = toArray(map($this->options()->only(self::HeaderOptions), fn(mixed $value): string => toString($value)));
        $headers["Date"] = currentDateTimeForHeader();
        $headers["x-ms-version"] = AbstractStorageService::VersionDefault;
        return $headers;
    }

    public function uri(): string
    {
        $query = toArray(map($this->options()->only(self::UriParameterOptions), fn(mixed $value): string => toString($value)));
        $query = (0 < count($query) ? "?" . http_build_query($query) : "");
        return "https://{$this->account()}.blob.core.windows.net/{$this->container()}/" . urlencode((string) $this->blobName()) . $query;
    }

    public function method(): string
    {
        return CommonConstants::MethodPut;
    }

    public function parseResponse(ResponseInterface $response): SetLegalHoldResponse
    {
        if (200 !== $response->getStatusCode()) {
            throw new BlobStorageException("Failed setting blob legal hold: {$response->getReasonPhrase()}");
        }

        $legalHoldResponse = new SetLegalHoldResponse();

        if ($response->hasHeader(Constants::HeaderVersion)) {
            $legalHoldResponse = $legalHoldResponse->withVersion(readSingleHeader($response, Constants::HeaderVersion));
        }

        if ($response->hasHeader(Constants::HeaderClientRequestId)) {
            $legalHoldResponse = $legalHoldResponse->withClientRequestId(readSingleHeader($response, Constants::HeaderClientRequestId));
        }

        if ($response->hasHeader(Constants::HeaderRequestId)) {
            $legalHoldResponse = $legalHoldResponse->withRequestId(readSingleHeader($response, Constants::HeaderRequestId));
        }

        if ($response->hasHeader(Constants::HeaderLegalHold)) {
            $legalHoldResponse = $legalHoldResponse->withLegalHold(parseBoolean(readSingleHeader($response, Constants::HeaderLegalHold)));
        }

        return $legalHoldResponse;
    }
}