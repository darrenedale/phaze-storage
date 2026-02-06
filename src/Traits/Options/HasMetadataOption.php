<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Options;

use Phaze\Storage\Constants;
use Phaze\Storage\Types\MetadataName;

trait HasMetadataOption
{
    abstract protected function option(string $name): mixed;

    abstract protected function setOption(string $name, mixed $value): void;

    abstract protected function clearOption(string $name): void;

    public function metadata(MetadataName $name): ?string
    {
        return $this->option(Constants::HeaderPrefixMeta . $name);
    }

    public function withMetadata(MetadataName $name, string $value): self
    {
        $clone = clone $this;
        $clone->setOption(Constants::HeaderPrefixMeta . $name, $value);
        return $clone;
    }

    public function withoutMetadata(MetadataName $name): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderPrefixMeta . $name);
        return $clone;
    }

    public function withoutAnyMetadata(): self
    {
        $clone = clone $this;
        $options = array_filter($clone->options(), fn(string $key): bool => str_starts_with($key, Constants::HeaderPrefixMeta));

        foreach (array_keys($options) as $metadataOption) {
            $clone->clearOption($metadataOption);
        }

        return $clone;
    }

}
