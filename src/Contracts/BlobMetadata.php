<?php

declare(strict_types=1);

namespace Phaze\Storage\Contracts;

interface BlobMetadata
{
    /** @return array<string,string> */
    public function metadata(): array;

    /** Immutably set some metadata. */
    public function withMetadata(string $name, string $value): self;
}
