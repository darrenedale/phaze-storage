<?php

declare(strict_types=1);

namespace Phaze\Storage\Options;

use Phaze\Storage\Traits\Options\HasCustomerEncryptionOptions;
use Phaze\Storage\Traits\Options\HasSnapshotAndVersionOptions;
use Phaze\Storage\Traits\Options\ExposesAllOptions;
use Phaze\Storage\Traits\Options\FiltersOptionsByKey;
use Phaze\Storage\Traits\Options\HasClientRequestIdOption;
use Phaze\Storage\Traits\Options\HasTimeoutOption;

class GetBlobProperties extends AbstractOptions
{
    use HasSnapshotAndVersionOptions;
    use HasCustomerEncryptionOptions;
    use HasClientRequestIdOption;
    use HasTimeoutOption;
    use FiltersOptionsByKey;
    use ExposesAllOptions;

    public function __destruct()
    {
        $this->scrubEncryptionKey();
        $this->scrubEncryptionKeySha256();
    }
}
