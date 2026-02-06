<?php

namespace Phaze\Storage\RestCommands\Blob;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use Phaze\Common\Constants;
use Phaze\Common\Traits\RestCommands\HasNoBody;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\BlobContainerInfo;
use Phaze\Storage\Options\ListContainers as ListContainersOptions;
use Phaze\Storage\Responses\ListContainers as ListContainersResponse;
use Phaze\Storage\Services\AbstractStorageService;
use Phaze\Storage\Types\AccountName;
use Phaze\Storage\Types\ContainerName;
use Psr\Http\Message\ResponseInterface;

use function Phaze\Common\Utilities\DateTime\currentDateTimeForHeader;
use function Phaze\Common\Utilities\String\parseBoolean;
use function Phaze\Common\Utilities\String\parseDateTime;
use function Phaze\Common\Utilities\String\parseInt;
use function Phaze\Common\Utilities\String\unquote;

/**
 * TODO x-ms-client-request-id header
 */
class ListContainers extends AbstractAccountCommand
{
    use HasNoBody;

    private ListContainersOptions $options;

    public function __construct(AccountName $account, ?ListContainersOptions $options = null)
    {
        parent::__construct($account);
        $this->options = $options ?? new ListContainersOptions();
    }

    public function headers(): array
    {
        return [
            "Date" => currentDateTimeForHeader(),
            "x-ms-version" => AbstractStorageService::VersionDefault,
        ];
    }

    public function options(): ListContainersOptions
    {
        return $this->options;
    }

    public function uri(): string
    {
        $options = (string) $this->options()->queryString();
        return "https://{$this->account()}.blob.core.windows.net/?comp=list" . ("" === $options ? "" : "&{$options}");
    }

    public function method(): string
    {
        return Constants::MethodGet;
    }

    private static function parsePropertiesElement(BlobContainerInfo $container, DOMElement $element): BlobContainerInfo
    {
        assert ("Properties" === $element->tagName);
        $element = $element->firstElementChild;

        while (null !== $element) {
            switch ($element->tagName) {
                case "Last-Modified":
                    $container = $container->withLastModified(parseDateTime(trim($element->textContent)));
                    break;

                case "Etag":
                    $container = $container->withEtag(unquote(trim($element->textContent)));
                    break;

                case "LeaseStatus":
                    $container = $container->withLeaseStatus(trim($element->textContent));
                    break;

                case "LeaseState":
                    $container = $container->withLeaseState(trim($element->textContent));
                    break;

                case "HasImmutabilityPolicy":
                    $container = $container->withImmutabilityPolicy(parseBoolean($element->textContent));
                    break;

                case "HasLegalHold":
                    $container = $container->withLegalHold(parseBoolean($element->textContent));
                    break;

                case "ImmutableStorageWithVersioningEnabled":
                    $container = $container->withImmutableStorageWithVersioningIsEnabled(parseBoolean($element->textContent));
                    break;
            }

            $element = $element->nextElementSibling;
        }

        return $container;

    }

    private static function parseMetadataElement(BlobContainerInfo $container, DOMElement $element): BlobContainerInfo
    {
        assert ("Metadata" === $element->tagName);
        $element = $element->firstElementChild;

        while (null !== $element) {
            if (!str_starts_with($element->tagName, "metadata-")) {
                throw new BlobStorageException("Expected XML child element named <metadata-*> in <Metadata>, found <{$element->tagName}>");
            }

            $container = $container->withMetadata(substr($element->tagName, 9), trim($element->textContent));
        }

        return $container;
    }

    private static function parseContainerElement(DOMElement $element): BlobContainerInfo
    {
        assert ("Container" === $element->tagName);
        $childElement =  $element->firstElementChild;
        $container = null;

        // look for the name element first so we can construct the Container
        while (null !== $childElement) {
            if ("Name" === $childElement->tagName) {
                $container = new BlobContainerInfo(new ContainerName(trim($childElement->textContent)));
                break;
            }

            $childElement = $childElement->nextElementSibling;
        }

        if (!$container) {
            throw new BlobStorageException("Expected <Name> XML element inside <Container> element not found");
        }

        $childElement =  $element->firstElementChild;

        while (null !== $childElement) {
            switch ($childElement->tagName) {
                case "Name":
                    break;

                case "Version":
                    $container = $container->withVersion(trim($childElement->textContent));
                    break;

                case "Deleted":
                    $container = $container->withDeleted(parseBoolean($childElement->textContent));
                    break;

                case "Properties":
                    $container = self::parsePropertiesElement($container, $childElement);
                    break;

                case "Metadata":
                    $container = self::parseMetadataElement($container, $childElement);
                    break;

                default:
                    throw new BlobStorageException("Expected valid XML child element for <Container>, found <{$element->tagName}>");
            }

            $childElement = $childElement->nextElementSibling;
        }

        return $container;
    }

    private static function parseContainersElement(DOMElement $element): array
    {
        assert ("Containers" === $element->tagName);
        $containers = [];
        $element =  $element->firstElementChild;

        while (null !== $element) {
            switch ($element->tagName) {
                case "Container":
                    $containers[] = self::parseContainerElement($element);
                    break;

                default:
                    throw new BlobStorageException("Expected <Container> XML element, found <{$element->tagName}>");
            }

            $element = $element->nextElementSibling;
        }

        return $containers;
    }

    /**
     * Parses the XML response body by recursive descent.
     *
     * @param ResponseInterface $response
     * @return ListContainersResponse
     * @throws BlobStorageException if the response is not well-formed XML or cannot be parsed.
     */
    public function parseResponse(ResponseInterface $response): ListContainersResponse
    {
        $doc = new DOMDocument();

        if (!$doc->loadXML((string) $response->getBody())) {
            throw new BlobStorageException("Expected valid XML response to ListContainers request");
        }

        /** @var DOMElement|null $root */
        $root = $doc->firstElementChild;

        if ("EnumerationResults" !== $root?->tagName) {
            throw new BlobStorageException("Expected \"EnumerationResults\" root XML element, found {$root?->tagName}");
        }

        /** @var DOMElement|null $element */
        $element = $root->firstElementChild;
        $containers = null;
        $marker = null;
        $nextMarker = null;
        $maxResults = null;

        while (null !== $element) {
            switch ($element->tagName) {
                case "MaxResults":
                    try {
                        $maxResults = parseInt(trim($element->textContent));
                    } catch (InvalidArgumentException $err) {
                        throw new BlobStorageException("Expected int value for <MaxResults> element content, found {$element->textContent}", previous: $err);
                    }
                    break;

                case "Containers":
                    $containers = self::parseContainersElement($element);
                    break;

                case "Marker":
                    $marker = trim($element->textContent);
                    break;

                case "NextMarker":
                    $nextMarker = trim($element->textContent);
                    break;

                default:
                    throw new BlobStorageException("Expected <Containers> or <NextMarker> XML element, found <{$element->tagName}>");
            }

            $element = $element->nextElementSibling;
        }

        if (null === $containers) {
            throw new BlobStorageException("Expected <Containers> XML element, none found");
        }

        if (null === $nextMarker) {
            throw new BlobStorageException("Expected <NextMarker> XML element, none found");
        }

        $containers = new ListContainersResponse($containers, $nextMarker);

        if (null !== $marker) {
            $containers = $containers->withMarker($marker);
        }

        return $containers->withMaxResults($maxResults);
    }
}
