<?php

declare(strict_types=1);

namespace Phaze\Storage\Contracts;

interface ResultsList
{
    /** Determine whether the list of results is complete, or there are more items to fetch. */
    public function isComplete(): bool;

    /**
     * Fetch the marker to send with the follow-up request for the next set of results if the list is not complete.
     *
     * @return string The marker, or an empty string if the list is complete.
     */
    public function nextMarker(): string;
}
