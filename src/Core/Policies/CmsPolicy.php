<?php

namespace HashtagCms\Core\Policies;

use HashtagCms\Models\CmsPermission;
use HashtagCms\User;

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
     * Determine whether the user can read the module.
     */
    public function read(?User $user, ?CmsPermission $permission, $resource = null): bool
    {
        return $this->canPerform($user, $permission, 'read', $resource);
    }

    /**
     * Determine whether the user can edit in the module.
     */
    public function edit(?User $user, ?CmsPermission $permission, $resource = null): bool
    {
        return $this->canPerform($user, $permission, 'edit', $resource);
    }

    /**
     * Determine whether the user can delete in the module.
     */
    public function delete(?User $user, ?CmsPermission $permission, $resource = null): bool
    {
        return $this->canPerform($user, $permission, 'delete', $resource);
    }

    /**
     * Determine whether the user can publish in the module.
     */
    public function publish(?User $user, ?CmsPermission $permission, $resource = null): bool
    {
        return $this->canPerform($user, $permission, 'publish', $resource);
    }

    /**
     * Determine whether the user can approve in the module.
     */
    public function approve(?User $user, ?CmsPermission $permission, $resource = null): bool
    {
        return $this->canPerform($user, $permission, 'approve', $resource);
    }

    // Keep Laravel standard names for compatibility if needed, mapping to our logic
    public function view(?User $user, ?CmsPermission $permission, $resource = null): bool
    {
        return $this->read($user, $permission, $resource);
    }

    public function create(?User $user, ?CmsPermission $permission, $resource = null): bool
    {
        return $this->edit($user, $permission, $resource);
    }

    public function update(?User $user, ?CmsPermission $permission, $resource = null): bool
    {
        return $this->edit($user, $permission, $resource);
    }
}
