<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits;

use Phaze\Storage\Types\ContainerName;

trait HasContainer
{
    private ContainerName $container;

    public function container(): ContainerName
    {
        return $this->container;
    }
}
