<?php

namespace Phaze\Storage\Traits;

use DOMElement;
use LogicException;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Tag;

trait ParsesTagsXml
{

    private static function parseTagElement(DOMElement $element): Tag
    {
        assert ("Tag" === $element->tagName, new LogicException("Expected parseTagElement() to be called with a <Tag> root element, found <{$element->tagName}>"));

        $key = null;
        $value = null;

        $element = $element->firstElementChild;

        while (null !== $element) {
            switch ($element->tagName) {
                case "Key":
                    $key = trim($element->textContent);
                    break;

                case "Value":
                    $value = trim($element->textContent);
                    break;

                default:
                    throw new BlobStorageException("Expected <Key> or <Value> element within <Tag> element, found <{$element->tagName}>");
            }

            $element = $element->nextElementSibling;
        }

        if (null === $key) {
            throw new BlobStorageException("Expected <Key> element within <Tag> element, none found");
        }

        if (null === $value) {
            throw new BlobStorageException("Expected <Value> element within <Tag> element, none found");
        }

        return new Tag($key, $value);
    }

    /** @return iterable<Tag> */
    private static function parseTagsElement(DOMElement $element): iterable
    {
        assert ("Tags" === $element->tagName, new LogicException("Expected parseTagsElement() to be called with a <Tags> root element, found <{$element->tagName}>"));
        $element = $element->firstElementChild;

        if (null === $element) {
            throw new BlobStorageException("Expected <TagSet> XML element inside <Tags> element not found");
        }

        if ("TagSet" !== $element->tagName) {
            throw new BlobStorageException("Expected <TagSet> XML element inside <Tags> element, found <{$element->tagName}>");
        }

        $tags = [];
        $element = $element->firstElementChild;

        while (null !== $element) {
            switch ($element->tagName) {
                case "Tag":
                    $tags[] = self::parseTagElement($element);
                    break;

                default:
                    throw new BlobStorageException("Expected <Tag> XML element inside <TagSet> element, found {$element->tagName}");
            }

            $element = $element->nextElementSibling;
        }

        return $tags;
    }
}
