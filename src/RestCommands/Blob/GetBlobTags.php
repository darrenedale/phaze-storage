<?php

declare(strict_types=1);

namespace Phaze\Storage\RestCommands\Blob;

use DOMDocument;
use DOMElement;
use Phaze\Common\Constants as CommonConstants;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Phaze\Storage\Constants;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Options\GetBlobTags as GetBlobTagsOptions;
use Phaze\Storage\Services\AbstractStorageService;
use Phaze\Storage\Tag;
use Phaze\Storage\Traits\ParsesTagsXml;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\BlobName;
use Phaze\Storage\Types\ContainerName;
use Psr\Http\Message\ResponseInterface;


use function Phaze\Common\Utilities\DateTime\currentDateTimeForHeader;

class GetBlobTags extends AbstractContainerCommand
{
    use HasNoBody;
    use ParsesTagsXml;

    private const UriParameterOptions = ["timeout", "snapshot", "versionid",];

    private const HeaderOptions = [Constants::HeaderLeaseId,];

    private BlobName $blobName;

    private GetBlobTagsOptions $options;

    public function __construct(AccountName $account, ContainerName $container, BlobName $blobName, ?GetBlobTagsOptions $options = null)
    {
        parent::__construct($account, $container);
        $this->blobName = $blobName;
        $this->options = $options ?? new GetBlobTagsOptions();
    }

    public function blobName(): BlobName
    {
        return $this->blobName;
    }

    public function options(): GetBlobTagsOptions
    {
        return $this->options;
    }

    public function headers(): array
    {
        // all these options are already strings
        $headers = $this->options()->only(self::HeaderOptions);
        $headers["Date"] = currentDateTimeForHeader();
        $headers["x-ms-version"] = AbstractStorageService::VersionDefault;
        return $headers;
    }

    public function uri(): string
    {
        $query = http_build_query($this->options()->only(self::UriParameterOptions));
        return "https://{$this->account()}.blob.core.windows.net/{$this->container()}/" . urlencode((string) $this->blobName()) . "?comp=tags" . ("" === $query ? "" : "&{$query}");
    }

    public function method(): string
    {
        return CommonConstants::MethodGet;
    }

    /** @return iterable<Tag> */
    public function parseResponse(ResponseInterface $response): iterable
    {
        if (200 !== $response->getStatusCode()) {
            throw new BlobStorageException("Failed fetching blob tags: {$response->getReasonPhrase()}");
        }

        $doc = new DOMDocument();

        if (!$doc->loadXML((string) $response->getBody())) {
            throw new BlobStorageException("Expected valid XML response to GetBlobTags request");
        }

        /** @var DOMElement|null $root */
        $root = $doc->firstElementChild;

        if ("Tags" !== $root?->tagName) {
            throw new BlobStorageException("Expected <Tags> root XML element, found {$root?->tagName}");
        }

        return self::parseTagsElement($root);
    }
}
