<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Options;

trait ExposesAllOptions
{
    abstract protected function options(): array;

    public function all(): array
    {
        return $this->options();
    }
}
