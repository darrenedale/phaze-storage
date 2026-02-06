<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Common\Contracts\RestCommand;
use Phaze\Storage\Traits\HasAccount;
use Phaze\Storage\Types\AccountName;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

abstract class AbstractAccountCommand implements RestCommand
{
    use HasAccount;

    public function __construct(AccountName $account)
    {
        $this->account = $account;
    }

    abstract public function headers(): array;

    abstract public function uri(): string;

    abstract public function method(): string;

    abstract public function body(): string|StreamInterface;

    abstract public function parseResponse(ResponseInterface $response): mixed;
}