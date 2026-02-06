<?php

declare(strict_types=1);

namespace Phaze\Storage;

use LogicException;
use Phaze\Storage\Contracts\ContentProvider as ContentProviderContract;
use Psr\Http\Message\StreamInterface;
use Stringable;

/** Wrapper for ContentProviders to enable them to be used easily in PSR7 requests. */
class ContentStream implements Stringable, StreamInterface
{

    /** @var int Read the content in 4k blocks. */
    private const ReadSize = 4096;

    private ContentProviderContract $content;

    public function __construct(ContentProviderContract $contentProvider)
    {
        $this->content = $contentProvider;
    }

    public function __toString(): string
    {
        if (0 < $this->content->position()) {
            $this->content->reset();
        }

        return $this->getContents();
    }

    public function close(): void
    {
    }

    public function detach(): void
    {
    }

    public function getSize(): int
    {
        return $this->content->length();
    }

    public function tell(): int
    {
        return $this->content->position();
    }

    public function eof(): bool
    {
        return $this->content->isFinished();
    }

    public function isSeekable(): bool
    {
        return false;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        throw new LogicException("ContentStream instances are not seekable");
    }

    public function rewind(): void
    {
        $this->content->reset();
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function write(string $string): int
    {
        throw new LogicException("ContentStream instances are not writable");
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function read(int $length): string
    {
        return $this->content->read($length);
    }

    public function getContents(): string
    {
        $content = "";

        while (!$this->content->isFinished()) {
            $content .= $this->content->read(self::ReadSize);
        }

        return $content;
    }

    public function getMetadata(?string $key = null)
    {
        return null;
    }
}