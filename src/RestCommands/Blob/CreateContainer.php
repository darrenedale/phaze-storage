<?php

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Common\Traits\RestCommands\HasNoBody;
use Phaze\Common\Traits\RestCommands\ResponseHasNoBody;
use Phaze\Storage\Services\AbstractStorageService;

use function Phaze\Common\Utilities\DateTime\currentDateTimeForHeader;

/**
 * TODO x-ms-client-request-id header
 */
class CreateContainer extends AbstractContainerCommand
{
    use HasNoBody;
    use ResponseHasNoBody;

    public function headers(): array
    {
        return [
            "Date" => currentDateTimeForHeader(),
            "x-ms-version" => AbstractStorageService::VersionDefault,
        ];
    }

    public function uri(): string
    {
        return "https://{$this->account()}.blob.core.windows.net/{$this->container()}?restype=container";
    }

    public function method(): string
    {
        return "PUT";
    }
}
