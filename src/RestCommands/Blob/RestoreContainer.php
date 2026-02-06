<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Common\Constants;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Phaze\Common\Traits\RestCommands\ResponseHasNoBody;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Services\AbstractStorageService;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\ContainerName;
use Phaze\Storage\Types\Version;
use Psr\Http\Message\ResponseInterface;

use function Phaze\Common\Utilities\DateTime\currentDateTimeForHeader;

class RestoreContainer extends AbstractContainerCommand
{
    use HasNoBody;
    use ResponseHasNoBody;

    private const DeletedContainerNameHeader = "x-ms-deleted-container-name";

    private const DeletedContainerVersionHeader = "x-ms-deleted-container-version";

    private Version $version;

    public function __construct(AccountName $account, ContainerName $container, Version $version)
    {
        parent::__construct($account, $container);
        $this->version = $version;
    }

    public function version(): Version
    {
        return $this->version;
    }

    public function uri(): string
    {
        return "https://{$this->account()}.blob.core.windows.net/{$this->container()}?restype=container&comp=undelete";
    }

    private function createErrorException(ResponseInterface $response): BlobStorageException
    {
        return new BlobStorageException("Failed to restore container {$this->container()}: {$response->getReasonPhrase()}");
    }

    public function headers(): array
    {
        return [
            "Date" => currentDateTimeForHeader(),
            "x-ms-version" => AbstractStorageService::VersionDefault,
            self::DeletedContainerNameHeader => (string) $this->container(),
            self::DeletedContainerVersionHeader => (string) $this->version(),
        ];
    }

    public function method(): string
    {
        return Constants::MethodPut;
    }
}