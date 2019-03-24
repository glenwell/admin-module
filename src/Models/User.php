<?php

namespace Modules\Admin\Models;

use TCG\Voyager\Traits\Resizable;
use Illuminate\Support\Facades\Cache;

class User extends \TCG\Voyager\Models\User
{
    use Resizable;

    public function hasPermission($name)
    {
        /* return Cache::remember('has_permission_' . $name, 60, function () use ($name) { */
            $this->loadPermissionsRelations();

            $_permissions = $this->roles_all()
                            ->pluck('permissions')->flatten()
                            ->pluck('key')->unique()->toArray();

            return in_array($name, $_permissions);
        /* }); */
    }

    private function loadRolesRelations()
    {
        /* return Cache::remember('role_roles', 60, function () { */
            if (!$this->relationLoaded('role')) {
                $this->load('role');
            }

            if (!$this->relationLoaded('roles')) {
                $this->load('roles');
            }
        /* }); */
    }

    private function loadPermissionsRelations()
    {
        $this->loadRolesRelations();

        /* return Cache::remember('role_permissions', 60, function () { */
            if (!$this->role->relationLoaded('permissions')) {
                $this->role->load('permissions');
                $this->load('roles.permissions');
            }
        /* }); */
    }
    
}
