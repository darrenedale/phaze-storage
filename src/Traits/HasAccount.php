<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits;

use Phaze\Storage\Types\AccountName;

trait HasAccount
{
    private AccountName $account;

    public function account(): AccountName
    {
        return $this->account;
    }
}
