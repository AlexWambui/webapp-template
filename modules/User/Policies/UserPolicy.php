<?php

namespace Modules\User\Policies;

use Illuminate\Auth\Access\Response;
use Modules\User\Models\User;
use Modules\User\Enums\UserRoles;

class UserPolicy
{
    public function create(User $auth): Response
    {
        if (! in_array($auth->role, [UserRoles::SUPER_ADMIN, UserRoles::ADMIN], true)) {
            return Response::deny('Only admins can create users.');
        }

        return Response::allow();
    }

    public function update(User $auth, User $target): Response
    {
        // Super admins can edit anyone
        if ($auth->role === UserRoles::SUPER_ADMIN) {
            return Response::allow();
        }

        if ($auth->role !== UserRoles::ADMIN) {
            return Response::deny('You do not have permission to edit users.');
        }

        // Admins cannot edit super admins
        if ($target->role === UserRoles::SUPER_ADMIN) {
            return Response::deny('Only super admins can edit super admins.');
        }

        return Response::allow();
    }

    public function delete(User $auth, User $target): Response
    {
        if ($auth->id === $target->id) {
            return Response::deny('You cannot delete your own account.');
        }

        return $this->update($auth, $target);
    }
}