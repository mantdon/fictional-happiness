<?php

namespace App\Services;

use App\Entity\User;

class RouteNameAppender
{
    /**
     * Is used when Controller is shared by employee and admin to decide to which route to redirect.
     * @param $user User
     * @param $routeName string
     * @return string
     */
    public function appendRoleToBeginning($user, $routeName): string
    {
        $roles = $user->getRoles();
        $role = $roles[0]; //Since every user has only one role in this project.
        $roleName = str_replace('ROLE_', '', $role);
        $roleName = strtolower($roleName);
        return $roleName . '_' . $routeName;
    }
}