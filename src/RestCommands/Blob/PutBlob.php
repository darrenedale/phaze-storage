<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use Phaze\Common\Constants as CommonConstants;
use Phaze\Storage\Constants;
use Phaze\Storage\Contracts\ContentProvider;
use Phaze\Storage\Contracts\ContentProvider as ContentProviderContract;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Options\PutBlob as PutBlobOptions;
use Phaze\Storage\Services\AbstractStorageService;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\BlobName;
use Phaze\Storage\Types\ContainerName;
use Phaze\Storage\ContentStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Stringable;

use function Phaze\Common\Utilities\DateTime\currentDateTimeForHeader;
use function Phaze\Common\Utilities\Iterable\map;
use function Phaze\Common\Utilities\Iterable\toArray;
use function Phaze\Common\Utilities\Casts\toString;

class PutBlob extends AbstractContainerCommand
{
    private BlobName $blobName;

    private string|ContentProviderContract $content;

    private PutBlobOptions $options;

    public function __construct(AccountName $account, ContainerName $container, BlobName $blobName, string|Stringable|ContentProviderContract $content, ?PutBlobOptions $options = null)
    {
        parent::__construct($account, $container);
        $this->blobName = $blobName;
        $this->content = $content;
        $this->options = $options ?? new PutBlobOptions();
    }

    public function blobName(): BlobName
    {
        return $this->blobName;
    }

    public function content(): string|ContentProviderContract
    {
        return $this->content;
    }

    public function options(): PutBlobOptions
    {
        return $this->options;
    }

    public function headers(): array
    {
        $options = $this->options();

        // all options are destined for headers and are Stringable, except:
        // - timeout is a URL parameter
        // - x-ms-tags is an array of tags that need to be converted to a query string for the header
        $headers = toArray(map($this->options()->except(["timeout", Constants::HeaderTags,]), fn (mixed $value): string => toString($value)));

        if (null !== $options->tags()) {
            $query = "";

            foreach ($options->tags() as $tag) {
                $query .= "&" . urlencode($tag->key()) . "=" . urlencode($tag->value());
            }
            
            if ("" !== $query) {
                $headers["x-ms-tags"] = substr($query, 1);
            }
        }

        
        $headers["Date"] = currentDateTimeForHeader();
        $headers["x-ms-version"] = AbstractStorageService::VersionDefault;
        $headers["Content-Length"] = match (true) {
            $this->content() instanceof ContentProvider => $this->content()->length(),
            default => strlen((string) $this->content()),
        };

        return $headers;
    }

    public function uri(): string
    {
        $query = "";

        if (null !== $this->options()->timeout()) {
            $query = "?timeout=" . urlencode((string) $this->options()->timeout());
        }

        return "https://{$this->account()}.blob.core.windows.net/{$this->container()}/" . urlencode((string) $this->blobName()) . $query;
    }

    public function method(): string
    {
        return CommonConstants::MethodPut;
    }

    public function body(): string|StreamInterface
    {
        $content = $this->content();

        if ($content instanceof ContentProvider) {
            return new ContentStream($content);
        }

        return (string) $content;
    }

    public function parseResponse(ResponseInterface $response): mixed
    {
        if (201 !== $response->getStatusCode()) {
            throw new BlobStorageException("Failed to put blob contents: {$response->getReasonPhrase()}");
        }

        return null;
    }
}
