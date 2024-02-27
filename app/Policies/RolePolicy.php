<?php

namespace App\Policies;

use App\Models\User;

class RolePolicy extends TaskPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAnyRoles($user)
    {
        $permissions = $this->getUserPermissions($user);

        if($permissions->contains('view-any-roles'))
        {
            return true;
        }

        return false;
    }

    public function createNewRoles($user)
    {
        $permissions = $this->getUserPermissions($user);

        if($permissions->contains('create-new-roles'))
        {
            return true;
        }

        return false;
    }

    public function updateAnyRoles($user)
    {
        $permissions = $this->getUserPermissions($user);

        if($permissions->contains('update-any-roles'))
        {
            return true;
        }

        return false;
    }

    public function deleteAnyRoles($user)
    {
        $permissions = $this->getUserPermissions($user);

        if($permissions->contains('delete-any-roles'))
        {
            return true;
        }

        return false;
    }

    public function viewUsersAndRoles($user)
    {
        $permissions = $this->getUserPermissions($user);

        if($permissions->contains('view-users-and-roles'))
        {
            return true;
        }

        return false;
    }

    public function manageUserRoles($user)
    {
        $permissions = $this->getUserPermissions($user);

        if($permissions->contains('manage-user-roles'))
        {
            return true;
        }

        return false;
    }
}
