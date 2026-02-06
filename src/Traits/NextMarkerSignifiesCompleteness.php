<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits;

/** Trait for results lists that have a next marker when they're incomplete and don't have one when they're complete. */
trait NextMarkerSignifiesCompleteness
{
    abstract public function nextMarker(): string;

    public function isComplete(): bool
    {
        return "" === $this->nextMarker();
    }
}
