<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Common\Constants;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Phaze\Common\Traits\RestCommands\ResponseHasNoBody;
use Phaze\Common\Types\Guid;
use Phaze\Storage\Services\AbstractStorageService;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\ContainerName;

use function Phaze\Common\Utilities\DateTime\currentDateTimeForHeader;

/**
 * TODO x-ms-client-request-id header
 */
class DeleteContainer extends AbstractContainerCommand
{
    use HasNoBody;
    use ResponseHasNoBody;

    private ?Guid $leaseId;

    public function __construct(AccountName $account, ContainerName $container, ?Guid $leaseId = null)
    {
        parent::__construct($account, $container);
        $this->leaseId = $leaseId;
    }

    public function leaseId(): ?string
    {
        return $this->leaseId;
    }

    /**
     * @inheritDoc
     */
    public function headers(): array
    {
        $headers = [
            "Date" => currentDateTimeForHeader(),
            "x-ms-version" => AbstractStorageService::VersionDefault,
        ];

        if (null !== $this->leaseId()) {
            $headers["x-ms-lease-id"] = (string) $this->leaseId();
        }

        return $headers;
    }

    public function uri(): string
    {
        return "https://{$this->account()}.blob.core.windows.net/{$this->container()}?restype=container";
    }

    public function method(): string
    {
        return Constants::MethodDelete;
    }
}
