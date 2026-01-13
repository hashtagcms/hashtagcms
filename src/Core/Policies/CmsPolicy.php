<?php

namespace HashtagCms\Core\Policies;

use Gate;
use Illuminate\Auth\Access\HandlesAuthorization;
use HashtagCms\Models\CmsPermission;

class CmsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view.
     *
     * @param  \HashtagCms\User  $user
     * @param  \HashtagCms\Models\CmsPermission  $crayonPermission
     * @return mixed
     */
    public function before($user)
    {

        if ($user->isSuperAdmin()) {
            return true;
        }

    }

    /**
     * Determine whether the user can view.
     *
     * @param  \HashtagCms\User  $user
     * @return mixed
     */
    public function view($user, CmsPermission $crayonPermission)
    {

        //info("CmsPolicy::view ".$user->id == $crayonPermission->user_id);
        return ($user->id == $crayonPermission->user_id) && ($this->hasPermissionTo('read') == true);

    }

    /**
     * Determine whether the user can create.
     *
     * @param  \HashtagCms\User  $user
     * @return mixed
     */
    public function create($user, CmsPermission $crayonPermission)
    {
        return ($user->id == $crayonPermission->user_id) && ($this->hasPermissionTo('edit') == true);
    }

    /**
     * Determine whether the user can update.
     *
     * @param  \HashtagCms\User  $user
     * @return mixed
     */
    public function update($user, CmsPermission $crayonPermission)
    {
        return ($user->id == $crayonPermission->user_id) && ($this->hasPermissionTo('edit') == true);
    }

    /**
     * Determine whether the user can delete.
     *
     * @param  \HashtagCms\User  $user
     * @return mixed
     */
    public function delete($user, CmsPermission $crayonPermission)
    {
        return ($user->id == $crayonPermission->user_id) && ($this->hasPermissionTo('delete') == true);
    }

    /**
     * Determine whether the user can publish
     *
     * @param  \HashtagCms\User  $user
     * @return mixed
     */
    public function publish($user, CmsPermission $crayonPermission)
    {
        return ($user->id == $crayonPermission->user_id) && ($this->hasPermissionTo('publish') == true);
    }

    /**
     * Determine whether the user can approve
     *
     * @param  \HashtagCms\User  $user
     * @return mixed
     */
    public function approve($user, CmsPermission $crayonPermission)
    {
        return ($user->id == $crayonPermission->user_id) && ($this->hasPermissionTo('approve') == true);
    }

    /**
     * Check permission
     *
     * @return bool
     */
    private function hasPermissionTo($permission)
    {
        return Gate::allows($permission) == true;
    }
}
