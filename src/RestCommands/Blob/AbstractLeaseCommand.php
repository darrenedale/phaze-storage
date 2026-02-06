<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Common\Constants;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Phaze\Storage\Services\AbstractStorageService;
use Psr\Http\Message\ResponseInterface;

use function Phaze\Common\Utilities\DateTime\currentDateTimeForHeader;

abstract class AbstractLeaseCommand extends AbstractContainerCommand
{
    protected const AcquireAction = "acquire";

    protected const RenewAction = "renew";

    protected const ChangeAction = "change";

    protected const ReleaseAction = "release";

    protected const BreakAction = "break";

    protected const LeaseIdHeader = "x-ms-lease-id";

    /** @var string Indicates the requested lease ID when acquiring/changing a lease. */
    protected const ProposedLeaseIdHeader = "x-ms-proposed-lease-id";

    /** @var string When requesting leases, this header indicates how long the lease should last. */
    protected const LeaseDurationHeader = "x-ms-lease-duration";

    /** @var string WHen breaking leases, this header indicates how long is left on the broken lease. */
    protected const LeaseTimeHeader = "x-ms-lease-time";

    use HasNoBody;

    abstract protected static function action(): string;

    public function headers(): array
    {
        return [
            "Date" => currentDateTimeForHeader(),
            "x-ms-version" => AbstractStorageService::VersionDefault,
            "x-ms-lease-action" => static::action(),
        ];
    }

    public function uri(): string
    {
        return "https://{$this->account()}.blob.core.windows.net/{$this->container()}?comp=lease&restype=container";
    }

    public function method(): string
    {
        return Constants::MethodPut;
    }

    abstract public function parseResponse(ResponseInterface $response): mixed;
}
