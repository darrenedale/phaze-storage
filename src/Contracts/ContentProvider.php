<?php

declare(strict_types=1);

namespace Phaze\Storage\Contracts;

use Phaze\Storage\Exceptions\ContentProviderException;

/**
 * Interface for objects that provide raw byte content.
 */
interface ContentProvider
{
    /**
     * Read and return up to a given number of bytets.
     *
     * Implementations may return fewer, including none (if no content is available), but must not return more.
     *
     * @throws ContentProviderException if reading is not possible.
     */
    public function read(int $size): string;

    /**
     * How many bytes have been provided by read()
     *
     * This is measured from the point the provider was created or was last reset(), whichever is more recent.
     */
    public function position(): int;

    /**
     * Check whether the provider has provided all its content.
     *
     * @return bool `true` if all the content has been read (including if it has none); `false` if there is more available.
     */
    public function isFinished(): bool;

    /**
     * Make the next read start at the beginning of the provider's content.
     *
     * @throws ContentProviderException if the provider is unable to reset.
     */
    public function reset(): void;

    /**
     * The total number of bytes the provider provides.
     *
     * @return int The number of bytes.
     */
    public function length(): int;
}
