<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Storage\Traits\HasContainer;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\ContainerName;

abstract class AbstractContainerCommand extends AbstractAccountCommand
{
    use HasContainer;

    public function __construct(AccountName $account, ContainerName $container)
    {
        parent::__construct($account);
        $this->container = $container;
    }
}
