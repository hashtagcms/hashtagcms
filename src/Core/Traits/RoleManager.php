<?php

namespace HashtagCms\Core\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use HashtagCms\Models\Role;

trait RoleManager
{
    public function isSuperAdmin()
    {

        return $this->hasRole('super-admin') || $this->hasRole('super-duper-admin');
    }

    public function roles()
    {

        return $this->belongsToMany(Role::class);

    }

    public function hasRole($role)
    {

        if (is_string($role)) {

            return $this->roles->contains('name', $role);

        }

        return (bool) $role->intersect($this->roles)->count();

    }

    /**
     * Save Role
     */
    public function assignRole($role)
    {

        $this->roles()->save($role);

    }

    /**
     * Save multiple roles
     */
    public function assignMultipleRole($roles)
    {

        foreach ($roles as $role) {
            $this->roles()->save($role);
        }

    }

    /**
     * Delete all roles
     *
     * @return mixed
     */
    public function detachAllRoles()
    {
        return DB::table('role_user')->where('user_id', $this->id)->delete();
    }

    /**
     * Sicne this is not eager fetch. let's create a new one
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function rights()
    {

        $permission = [];
        $user = self::find($this->id);
        //echo "id: ".$this->id;
        if ($user->hasRole('super-admin') || $this->hasRole('super-duper-admin')) {
        } else {
            //can have multiple roles
            $roles = self::with('roles')->find($user->id)->roles->toArray();
            foreach ($roles as $role) {
                $permission[] = Role::with('permissions')->find($role['id'])->permissions->pluck('name');
            }
        }

        return array_unique(Arr::flatten($permission));
    }
}
