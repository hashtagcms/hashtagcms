<?php

namespace HashtagCms\Core\Policies;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;
use HashtagCms\Models\CmsPermission;
use HashtagCms\Models\User;

/**
 * Abstract Class BaseCmsPolicy
 *
 * Base policy class for all CMS module policies.
 *
 * Provides common functionality for:
 * - SuperAdmin bypass checks
 * - Permission verification (module access + role permissions)
 * - Permission caching
 * - Debug logging (automatically enabled/disabled based on APP_ENV)
 * - Null safety
 *
 * Debug Logging:
 * - Automatically ENABLED in: local, development, dev, staging
 * - Automatically DISABLED in: production, prod
 * - Can be manually controlled via enableDebugLogging() / disableDebugLogging()
 *
 * This class can be extended by specific policies to inherit common behavior
 * while allowing customization for specific modules.
 *
 * @package HashtagCms\Core\Policies
 */
abstract class BaseCmsPolicy
{
    use HandlesAuthorization;

    /**
     * Cache for permission checks to reduce database queries
     *
     * @var array
     */
    private $permissionCache = [];

    /**
     * Enable or disable debug logging
     *
     * Automatically set based on APP_ENV:
     * - Enabled for: local, development, dev, staging
     * - Disabled for: production, prod
     *
     * @var bool
     */
    protected $enableDebugLogging;

    /**
     * Constructor
     *
     * Initializes debug logging based on APP_ENV.
     */
    public function __construct()
    {
        $this->enableDebugLogging = $this->shouldEnableDebugLogging();
    }

    /**
     * Determine if debug logging should be enabled based on environment.
     *
     * @return bool
     */
    protected function shouldEnableDebugLogging(): bool
    {
        $env = config('app.env', 'production');

        // Enable logging for development environments
        $debugEnvironments = ['local', 'development', 'dev', 'staging'];

        return in_array($env, $debugEnvironments);
    }

    /**
     * Perform pre-authorization checks.
     *
     * SuperAdmins bypass all permission checks.
     *
     * @param  \HashtagCms\Models\User|null  $user
     * @return bool|null
     */
    public function before(?User $user): ?bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Check if user can perform an action on the module.
     *
     * This is the core permission check that verifies:
     * 1. User and permission are not null
     * 2. The permission record belongs to this user
     * 3. User's role has the required permission
     *
     * @param  \HashtagCms\Models\User|null  $user
     * @param  \HashtagCms\Models\CmsPermission|null  $permission
     * @param  string  $action
     * @return bool
     */
    protected function canPerform(?User $user, ?CmsPermission $permission, string $action): bool
    {
        // Null safety check
        if (!$user || !$permission) {
            $this->logPermissionDenial(null, null, $action, 'null_user_or_permission');
            return false;
        }

        // Check if this permission belongs to the user
        $isOwnPermission = $this->isOwnPermission($user, $permission);

        // Check if user's role has the required permission
        $hasPermission = $this->hasPermissionTo($action);

        // Log if permission is denied
        if (!$isOwnPermission || !$hasPermission) {
            $this->logPermissionDenial($user, $permission, $action, [
                'is_own_permission' => $isOwnPermission,
                'has_permission' => $hasPermission,
            ]);
        }

        return $isOwnPermission && $hasPermission;
    }

    /**
     * Check if the permission record belongs to the user.
     *
     * @param  \HashtagCms\Models\User  $user
     * @param  \HashtagCms\Models\CmsPermission  $permission
     * @return bool
     */
    protected function isOwnPermission(User $user, CmsPermission $permission): bool
    {
        return $user->id === $permission->user_id;
    }

    /**
     * Check if user has the specified permission via their role.
     *
     * Uses caching to reduce database queries within the same request.
     *
     * @param  string  $permission
     * @return bool
     */
    protected function hasPermissionTo(string $permission): bool
    {
        // Check cache first
        if (!isset($this->permissionCache[$permission])) {
            $this->permissionCache[$permission] = Gate::allows($permission);
        }

        return $this->permissionCache[$permission];
    }

    /**
     * Log permission denial for debugging and auditing.
     *
     * @param  \HashtagCms\Models\User|null  $user
     * @param  \HashtagCms\Models\CmsPermission|null  $permission
     * @param  string  $action
     * @param  mixed  $reason
     * @return void
     */
    protected function logPermissionDenial(?User $user, ?CmsPermission $permission, string $action, $reason): void
    {
        if (!$this->enableDebugLogging) {
            return;
        }

        $context = [
            'policy' => static::class,
            'action' => $action,
            'reason' => $reason,
        ];

        if ($user) {
            $context['user_id'] = $user->id;
            $context['user_email'] = $user->email ?? 'N/A';
        }

        if ($permission) {
            $context['permission_user_id'] = $permission->user_id;
            $context['permission_module_id'] = $permission->module_id ?? 'N/A';
            $context['permission_readonly'] = $permission->readonly ?? 'N/A';
        }

        \Log::debug('CmsPolicy permission denied', $context);
    }

    /**
     * Clear the permission cache.
     *
     * Useful for testing or when permissions change during a request.
     *
     * @return void
     */
    public function clearPermissionCache(): void
    {
        $this->permissionCache = [];
    }

    /**
     * Enable debug logging.
     *
     * @return void
     */
    public function enableDebugLogging(): void
    {
        $this->enableDebugLogging = true;
    }

    /**
     * Disable debug logging.
     *
     * @return void
     */
    public function disableDebugLogging(): void
    {
        $this->enableDebugLogging = false;
    }
}
