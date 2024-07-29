<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\LazyCollection;

class UserRepository
{
    public function __construct(private User $model)
    {
    }

    public function getBannedUsers(
        bool $activeUsersOnly = false,
        bool $withTrashed = false,
        bool $trashedOnly = false,
        bool $noAdmin = false,
        bool $adminOnly = false,
        string $sortBy = 'email'
    ): LazyCollection {
        $query = $this->model->query();

        if ($withTrashed) {
            $query->withTrashed();
        } elseif ($trashedOnly) {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }
        /** @uses User::scopeBanned() */
        $query->banned();

        if ($activeUsersOnly) {
            /** @uses User::scopeActivated() */
            $query->activated();
        }

        if ($noAdmin) {
            /** @uses User::scopeNoAdmin() */
            $query->noAdmin();
        } elseif ($adminOnly) {
            /** @uses User::scopeAdmin() */
            $query->admin();
        }

        $query->orderBy($sortBy);

        return $query->lazy();
    }
}
