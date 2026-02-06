<?php

declare(strict_types=1);

namespace Phaze\Storage\Traits\Blob;

use Phaze\Storage\Types\Permissions;

trait HasOwnershipAndPermissions
{
    private string $owner = "";

    private string $group = "";

    private ?Permissions $permissions = null;

    public function owner(): string
    {
        return $this->owner;
    }

    public function withOwner(string $owner): self
    {
        $clone = clone $this;
        $clone->owner = $owner;
        return $clone;
    }

    public function group(): string
    {
        return $this->group;
    }

    public function withGroup(string $group): self
    {
        $clone = clone $this;
        $clone->group = $group;
        return $clone;
    }

    public function permissions(): ?Permissions
    {
        return $this->permissions;
    }

    public function withPermissions(?Permissions $permissions): self
    {
        $clone = clone $this;
        $clone->permissions = $permissions;
        return $clone;
    }

}
