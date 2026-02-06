<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use Phaze\Common\Types\UnsignedInteger;

trait HasContentDetails
{
    private string $contentType = "";

    private string $contentEncoding = "";

    private ?UnsignedInteger $contentLength = null;

    private string $contentLanguage = "";

    private string $contentDisposition = "";

    private string $cacheControl = "";


    public function contentType(): string
    {
        return $this->contentType;
    }

    public function withContentType(string $contentType): self
    {
        $clone = clone $this;
        $clone->contentType = $contentType;
        return $clone;
    }

    public function contentEncoding(): string
    {
        return $this->contentEncoding;
    }

    public function withContentEncoding(string $encoding): self
    {
        $clone = clone $this;
        $clone->contentEncoding = $encoding;
        return $clone;
    }

    public function contentLength(): ?UnsignedInteger
    {
        return $this->contentLength;
    }

    public function withContentLength(?UnsignedInteger $length): self
    {
        $clone = clone $this;
        $clone->contentLength = $length;
        return $clone;
    }

    public function contentLanguage(): string
    {
        return $this->contentLanguage;
    }

    public function withContentLanguage(string $language): self
    {
        $clone = clone $this;
        $clone->contentLanguage = $language;
        return $clone;
    }

    public function contentDisposition(): string
    {
        return $this->contentDisposition;
    }

    public function withContentDisposition(string $contentDisposition): self
    {
        $clone = clone $this;
        $clone->contentDisposition = $contentDisposition;
        return $clone;
    }

    public function cacheControl(): string
    {
        return $this->cacheControl;
    }

    public function withCacheControl(string $cacheControl): self
    {
        $clone = clone $this;
        $clone->cacheControl = $cacheControl;
        return $clone;
    }
}
