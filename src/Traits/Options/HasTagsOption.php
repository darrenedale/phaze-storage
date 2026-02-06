<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Options;

use Phaze\Storage\Constants;
use Phaze\Storage\Tag;
use TypeError;

use function Phaze\Common\Utilities\Iterable\all;

trait HasTagsOption
{
    abstract protected function option(string $name): mixed;

    abstract protected function setOption(string $name, mixed $value): void;

    abstract protected function clearOption(string $name): void;


    /** @return null|Tag[] */
    public function tags(): ?array
    {
        return $this->option(Constants::HeaderTags);
    }

    public function withTag(Tag $tag): self
    {
        $clone = clone $this;
        $tags = $clone->tags();
        $tags[] = $tag;
        $clone->setOption(Constants::HeaderTags, $tags);
        return $clone;
    }

    /**
     * Tags are not supported for hierarchical namespace accounts as at January 2024.
     *
     * @param Tag[] $tags
     * @return self
     */
    public function withTags(array $tags): self
    {
        assert (all($tags, fn (mixed $tag): bool => $tag instanceof Tag), new TypeError("Expected array of Tag instances, found non-Tag"));
        $clone = clone $this;
        $clone->setOption(Constants::HeaderTags, $tags);
        return $clone;
    }

    public function withoutTags(): self
    {
        $clone = clone $this;
        $clone->clearOption(Constants::HeaderTags);
        return $clone;
    }
}
