<?php

declare(strict_types=1);

namespace Phaze\Storage\Responses;

use Phaze\Common\Contracts\Types\Boolable;
use Phaze\Storage\Traits\Blob\HasClientRequestId;
use Phaze\Storage\Traits\Blob\HasLegalHold;
use Phaze\Storage\Traits\Blob\HasRequestId;
use Phaze\Storage\Traits\Blob\HasRestApiVersion;

class SetLegalHold implements Boolable
{
    use HasClientRequestId;
    use HasRestApiVersion;
    use HasLegalHold;
    use HasRequestId;

    public function toBoolean(): bool
    {
        return $this->legalHold();
    }
}