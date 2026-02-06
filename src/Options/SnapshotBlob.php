<?php

declare(strict_types=1);

namespace Phaze\Storage\Options;

use Phaze\Storage\Traits\Options\HasCustomerEncryptionOptions;
use Phaze\Storage\Traits\Options\HasEncryptionScopeOption;
use Phaze\Storage\Traits\Options\HasLeaseIdOption;
use Phaze\Storage\Traits\Options\HasMetadataOption;
use Phaze\Storage\Traits\Options\ExposesAllOptions;
use Phaze\Storage\Traits\Options\FiltersOptionsByKey;
use Phaze\Storage\Traits\Options\HasClientRequestIdOption;
use Phaze\Storage\Traits\Options\HasTimeoutOption;

class SnapshotBlob extends AbstractOptions
{
    use HasLeaseIdOption;
    use HasClientRequestIdOption;
    use HasCustomerEncryptionOptions;
    use HasEncryptionScopeOption;
    use HasMetadataOption;
    use HasTimeoutOption;
    use FiltersOptionsByKey;
    use ExposesAllOptions;
}
