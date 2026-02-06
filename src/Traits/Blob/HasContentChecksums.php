<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

/**
 * MD5 and CRC64 checksums for response content.
 *
 * Note that these are intended to capture the checksums of the content of a response. They are not intended to capture
 * the checksums of the content of blobs per se. For example, if the request includes a Range header, the response will
 * contain only the requested portion of the blob. The checksums are for that portion of the content.
 *
 * For the checkums for the full content of the blobs from which the response content is taken, see XXXXX.
 */
trait HasContentChecksums
{
    private string $contentCrc64 = "";

    private string $contentMd5 = "";

    public function contentCrc64(): string
    {
        return $this->contentCrc64;
    }

    public function withContentCrc64(string $contentCrc64): self
    {
        $clone = clone $this;
        $clone->contentCrc64 = $contentCrc64;
        return $clone;
    }

    public function contentMd5(): string
    {
        return $this->contentMd5;
    }

    public function withContentMd5(string $contentMd5): self
    {
        $clone = clone $this;
        $clone->contentMd5 = $contentMd5;
        return $clone;
    }
}
