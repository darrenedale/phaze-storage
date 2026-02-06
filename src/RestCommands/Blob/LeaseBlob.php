<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Common\Constants as CommonConstants;
use Phaze\Common\Exceptions\InvalidValueException;
use Phaze\Storage\Constants;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Options\LeaseBlob as LeaseBlobOptions;
use Phaze\Storage\Responses\LeaseBlob as LeaseBlobResponse;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\BlobName;
use Phaze\Storage\Types\ContainerName;
use Phaze\Storage\Types\LeaseBlobAction;
use Phaze\Storage\Types\LeaseTime;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Phaze\Common\Types\Guid;
use Psr\Http\Message\ResponseInterface;

use function Phaze\Common\Utilities\Iterable\map;
use function Phaze\Common\Utilities\Iterable\toArray;
use function Phaze\Common\Utilities\Response\readSingleHeader;
use function Phaze\Common\Utilities\String\parseDateTime;
use function Phaze\Common\Utilities\String\parseInt;

class LeaseBlob extends AbstractContainerCommand
{
    use HasNoBody;

    private const HeaderOptions = [
        Constants::HeaderLeaseDuration,
        Constants::HeaderLeaseId,
        Constants::HeaderProposedLeaseId,
        Constants::HeaderLeaseBreakPeriod,
    ];

    private BlobName $blobName;

    private LeaseBlobAction $action;

    private LeaseBlobOptions $options;

    public function __construct(AccountName $account, ContainerName $container, BlobName $blob, LeaseBlobAction $action, ?LeaseBlobOptions $options = null)
    {
        parent::__construct($account, $container);
        $this->blobName = $blob;
        $this->action = $action;
        $this->options = $options ?? new LeaseBlobOptions();
    }

    public function blobName(): BlobName
    {
        return $this->blobName;
    }

    public function options(): LeaseBlobOptions
    {
        return $this->options;
    }

    public function action(): LeaseBlobAction
    {
        return $this->action;
    }

    public function headers(): array
    {
        $headers = toArray(map($this->options()->only(self::HeaderOptions), fn (mixed $value): string => (string) $value));
        $headers[Constants::HeaderLeaseBlobAction] = (string) $this->action();
        return $headers;
    }

    public function uri(): string
    {
        $queryArgs = $this->options()->only(["timeout"]);
        $queryArgs["comp"] = "lease";
        return "https://{$this->account()}.blob.core.windows.net/{$this->container()}/{$this->blobName()}?" . http_build_query($queryArgs);
    }

    public function method(): string
    {
        return CommonConstants::MethodPut;
    }

    public function parseResponse(ResponseInterface $response): LeaseBlobResponse
    {
        if ($response->getStatusCode() !== match ((string) $this->action()) {
            LeaseBlobAction::Acquire => 201,
            LeaseBlobAction::Renew, LeaseBlobAction::Change, LeaseBlobAction::Release => 200,
            LeaseBlobAction::Break => 202,
        }) {
            throw new BlobStorageException("Failed to {$this->action()} blob lease: {$response->getStatusCode()} {$response->getReasonPhrase()}");
        }

        try {
            $leaseInfo = new LeaseBlobResponse(
                new Guid(readSingleHeader($response, Constants::HeaderLeaseId)),
                $response->hasHeader(Constants::HeaderLeaseTime) ? new LeaseTime(parseInt(readSingleHeader($response, Constants::HeaderLeaseTime))) : null
            );

            if ($response->hasHeader(Constants::HeaderVersion)) {
                $leaseInfo = $leaseInfo->withVersion(readSingleHeader($response, Constants::HeaderVersion));
            }

            if ($response->hasHeader("Date")) {
                $leaseInfo = $leaseInfo->withDate(parseDateTime(readSingleHeader($response, "Date"), DATE_RFC1123));
            }

            if ($response->hasHeader("ETag")) {
                $leaseInfo = $leaseInfo->withEtag(readSingleHeader($response, "ETag"));
            }

            if ($response->hasHeader("Last-Modified")) {
                $leaseInfo = $leaseInfo->withLastModified(parseDateTime(readSingleHeader($response, "Last-Modified"), DATE_RFC1123));
            }

            return $leaseInfo;
        } catch (InvalidValueException $err) {
            throw new BlobStorageException("Expected valid Lease Blob response headers: {$err->getMessage()}");
        }
    }
}