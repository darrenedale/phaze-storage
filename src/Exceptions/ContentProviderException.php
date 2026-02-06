<?php

declare(strict_types=1);

namespace Phaze\Storage\Exceptions;

use Phaze\Common\Exceptions\PhazeException;

/** Thrown when a content provider is asked to do something it's unable to. */
class ContentProviderException extends PhazeException
{
}
