<?php

declare(strict_types=1);

namespace Phaze\Storage\Options;

use Phaze\Common\Types\UnsignedInteger;
use Phaze\Storage\Types\ListBlobsIncludeDatasets;
use Phaze\Storage\Types\ListBlobsShowOnlyDatasets;
use Phaze\Storage\Traits\Options\HasTimeoutOption;
use Phaze\Storage\Traits\Options\NaivelyBuildsOptionsQueryString;

class ListBlobs extends AbstractOptions
{
    use HasTimeoutOption;
    use NaivelyBuildsOptionsQueryString;

    public function prefix(): ?string
    {
        return $this->option("prefix");
    }

    public function withPrefix(string $prefix): self
    {
        $clone = clone $this;
        $clone->setOption("prefix", $prefix);
        return $clone;
    }

    public function delimiter(): ?string
    {
        return $this->option("delimiter");
    }

    public function withDelimiter(string $delimiter): self
    {
        $clone = clone $this;
        $clone->setOption("delimiter", $delimiter);
        return $clone;
    }

    public function marker(): ?string
    {
        return $this->option("marker");
    }

    public function withMarker(string $marker): self
    {
        $clone = clone $this;
        $clone->setOption("marker", $marker);
        return $clone;
    }

    public function maxResults(): ?int
    {
        return $this->option("maxresults");
    }

    public function withMaxResults(UnsignedInteger $maxResults): self
    {
        $clone = clone $this;
        $clone->setOption("maxresults", clone $maxResults);
        return $clone;
    }

    public function include(): ?ListBlobsIncludeDatasets
    {
        return $this->option("include");
    }

    public function withInclude(ListBlobsIncludeDatasets $include): self
    {
        $clone = clone $this;
        $clone->setOption("include", clone $include);
        return $clone;
    }

    public function showOnly(): ?ListBlobsShowOnlyDatasets
    {
        return $this->option("showonly");
    }

    public function withShowOnly(ListBlobsShowOnlyDatasets $showOnly): self
    {
        $clone = clone $this;
        $clone->setOption("showonly", $showOnly);
        return $clone;
    }
}
