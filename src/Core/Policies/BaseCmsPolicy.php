<?php

namespace HashtagCms\Core\Policies;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;
use HashtagCms\Models\CmsPermission;
use HashtagCms\User;

/**
 * Abstract Class BaseCmsPolicy
 *
 * Base policy class for all CMS module policies.
 *
 * Provides common functionality for:
 * - SuperAdmin bypass checks
 * - Permission verification (module access + role permissions)
 * This class provides:
 * - Dynamic Action Support: Using __call() to handle any permission added to the DB.
 * - SuperAdmin bypass checks (SuperAdmins can do anything).
 * - Permission verification (module access + role permissions).
 * - Resource Ownership: Contributors can only touch their own data.
 * - Permission caching: Multi-layered caching (Boot, Session, and request-level).
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
     * @param  User|null  $user
     * @return bool|null
     */
    public function before(?User $user): ?bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->hasRole('admin')) {
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
     * @param  User|null  $user
     * @param  CmsPermission|null  $permission
     * @param  string  $action
     * @return bool
     */
    protected function canPerform(?User $user, ?CmsPermission $permission, string $action, $resource = null): bool
    {
        // Null safety check
        if (!$user || !$permission) {
            $this->logPermissionDenial(null, null, $action, 'null_user_or_permission');
            return false;
        }

        // Check if this permission record belongs to the user
        $isOwnPermission = $this->isOwnPermission($user, $permission);

        // Check if user's role has the required permission
        $hasPermission = $this->hasPermissionTo($user, $action);

        // Handle Contributor "own content" restriction
        $isOwner = true;
        if ($resource !== null && $user->isContributor()) {
            $isOwner = $this->isOwner($user, $resource);
        }

        // Log if permission is denied
        if (!$isOwnPermission || !$hasPermission || !$isOwner) {
            $this->logPermissionDenial($user, $permission, $action, [
                'is_own_permission' => $isOwnPermission,
                'has_permission' => $hasPermission,
                'is_owner' => $isOwner,
            ]);
        }

        return $isOwnPermission && $hasPermission && $isOwner;
    }

    /**
     * Check if the user is the owner of the resource.
     *
     * @param  User  $user
     * @param  mixed  $resource
     * @return bool
     */
    protected function isOwner(User $user, $resource): bool
    {
        if ($resource === null) {
            return true;
        }

        // In case resource is an array (sometimes toArray() is used)
        if (is_array($resource)) {
            // If it's an empty array, it's likely a 'create' action where no data exists yet
            if (empty($resource)) {
                return true;
            }
            $ownerId = $resource['insert_by'] ?? $resource['user_id'] ?? null;
            return $ownerId !== null && (int)$ownerId === (int)$user->id;
        }

        // Standard Eloquent model check
        $ownerId = $resource->insert_by ?? $resource->user_id ?? null;
        
        return $ownerId !== null && (int)$ownerId === (int)$user->id;
    }

    /**
     * Check if the permission record belongs to the user.
     *
     * @param  User  $user
     * @param  CmsPermission  $permission
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
     * @param  User  $user
     * @param  string  $permission
     * @return bool
     */
    protected function hasPermissionTo(User $user, string $permission): bool
    {
        $cacheKey = $user->id . '_' . $permission;

        // Check cache first
        if (!isset($this->permissionCache[$cacheKey])) {
            $this->permissionCache[$cacheKey] = Gate::forUser($user)->allows($permission);
        }

        return $this->permissionCache[$cacheKey];
    }

    /**
     * Log permission denial for debugging and auditing.
     *
     * @param  User|null  $user
     * @param  CmsPermission|null  $permission
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
     * Handle dynamic permission methods.
     *
     * This allows the policy to handle any action defined in the database
     * (e.g., 'export', 'duplicate', 'assign') without needing explicit methods
     * for each action in the policy class.
     *
     * @param string $name The action name being checked
     * @param array $arguments [0 => User $user, 1 => CmsPermission $permission, 2 => mixed $resource]
     * @return bool
     */
    public function __call($name, $arguments)
    {
        // Laravel passes the User as the first argument, followed by what was passed to the Gate call.
        // In this project, Viewer::checkPolicy passes [$permission, $resource].
        return $this->canPerform(
            $arguments[0] ?? null, // User
            $arguments[1] ?? null, // CmsPermission Model
            $name,                 // The dynamic action name
            $arguments[2] ?? null  // The specific resource/model
        );
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
