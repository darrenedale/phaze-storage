<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use InvalidArgumentException;

use function Phaze\Common\Utilities\Iterable\all;

trait HasMetadata
{
    /** @var array<string,string>  */
    private array $metadata = [];

    /** @return array<string,string> */
    public function metadata(): array
    {
        return $this->metadata;
    }

    public function withMetadata(string $name, string $value): self
    {
        $clone = clone $this;
        $clone->metadata[$name] = $value;
        return $clone;
    }

    public function withReplacementMetadata(array $data): self
    {
        if (!all($data, fn (mixed $value, mixed $key): bool => is_string($key) && is_string($value))) {
            throw new InvalidArgumentException("Expected a dictionary of string keys and values, found a non-string key or value");
        }

        $clone = clone $this;
        $clone->metadata = $data;
        return $clone;
    }
}
