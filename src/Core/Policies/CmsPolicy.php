<?php

namespace HashtagCms\Core\Policies;

use HashtagCms\Models\CmsPermission;
use HashtagCms\Models\User;

/**
 * Class CmsPolicy
 *
 * Policy for checking user permissions on CMS modules.
 *
 * This policy verifies:
 * 1. User has access to the module (via cms_permissions table)
 * 2. User's role has the required permission (read, edit, delete, etc.)
 *
 * Note: SuperAdmins bypass all checks via the before() method (inherited from BaseCmsPolicy).
 * Note: The 'readonly' flag is checked in Viewer::checkPolicy(), not here.
 *       readonly=1 only blocks 'edit' operations, not delete/publish/approve.
 *
 * @package HashtagCms\Core\Policies
 */
class CmsPolicy extends BaseCmsPolicy
{
    /**
     * Determine whether the user can view the module.
     *
     * Checks if the user has access to the module and has 'read' permission via their role.
     *
     * @param  \HashtagCms\Models\User|null  $user
     * @param  \HashtagCms\Models\CmsPermission|null  $permission
     * @return bool
     */
    public function view(?User $user, ?CmsPermission $permission): bool
    {
        return $this->canPerform($user, $permission, 'read');
    }

    /**
     * Determine whether the user can create in the module.
     *
     * Checks if the user has access to the module and has 'edit' permission via their role.
     *
     * @param  \HashtagCms\Models\User|null  $user
     * @param  \HashtagCms\Models\CmsPermission|null  $permission
     * @return bool
     */
    public function create(?User $user, ?CmsPermission $permission): bool
    {
        return $this->canPerform($user, $permission, 'edit');
    }

    /**
     * Determine whether the user can update in the module.
     *
     * Checks if the user has access to the module and has 'edit' permission via their role.
     *
     * @param  \HashtagCms\Models\User|null  $user
     * @param  \HashtagCms\Models\CmsPermission|null  $permission
     * @return bool
     */
    public function update(?User $user, ?CmsPermission $permission): bool
    {
        return $this->canPerform($user, $permission, 'edit');
    }

    /**
     * Determine whether the user can delete in the module.
     *
     * Checks if the user has access to the module and has 'delete' permission via their role.
     *
     * @param  \HashtagCms\Models\User|null  $user
     * @param  \HashtagCms\Models\CmsPermission|null  $permission
     * @return bool
     */
    public function delete(?User $user, ?CmsPermission $permission): bool
    {
        return $this->canPerform($user, $permission, 'delete');
    }

    /**
     * Determine whether the user can publish in the module.
     *
     * Checks if the user has access to the module and has 'publish' permission via their role.
     *
     * @param  \HashtagCms\Models\User|null  $user
     * @param  \HashtagCms\Models\CmsPermission|null  $permission
     * @return bool
     */
    public function publish(?User $user, ?CmsPermission $permission): bool
    {
        return $this->canPerform($user, $permission, 'publish');
    }

    /**
     * Determine whether the user can approve in the module.
     *
     * Checks if the user has access to the module and has 'approve' permission via their role.
     *
     * @param  \HashtagCms\Models\User|null  $user
     * @param  \HashtagCms\Models\CmsPermission|null  $permission
     * @return bool
     */
    public function approve(?User $user, ?CmsPermission $permission): bool
    {
        return $this->canPerform($user, $permission, 'approve');
    }
}
