# Administrative Permissions Architecture

HashtagCMS uses a multi-layered, dynamic Permission and RBAC (Role-Based Access Control) system tailored for the complexities of a dynamic, database-driven CMS.

## 1. Core Concepts

### Dynamic Routing & Permission Mapping
Unlike static Laravel applications where permissions are often hard-coded into route middleware, HashtagCMS maps permissions dynamically.
- **Modules**: Definitions are stored in the `cms_modules` table.
- **Controllers**: Controllers are looked up by the `controller_name` in the database.
- **Actions**: Permissions (Rights) are checked against the module's target controller and action.

---

## 2. The Authorization Layers

### Layer 1: The Boot Layer (Gate Definitions)
During the application boot process, `AdminServiceProvider` iterates through all defined permissions and registers them as Laravel Gates.
- **Implementation**: `AdminServiceProvider::loadPermissions()`
- **Logic**: A closure is bound to each permission name (e.g., `read`, `publish`).
- **Update**: We solved a critical bug where role comparisons failed due to object-level intersection. We now use **ID-based intersection** for reliability.

### Layer 2: The Module Entry Layer (Trait Check)
Every admin controller uses the `Viewer` trait. Before any request is processed, the system identifies the target module and checks the user's base access.
- **Implementation**: `Viewer::checkPolicy($rights, $resource, $module)`
- **Mechanism**: Fetches `CmsPermission` for the current user and module. If the user isn't assigned to that module in `cms_permissions`, access is denied immediately.

### Layer 3: The Data Layer (Policy Enforcement)
Once inside a module, the system uses Laravel Policies to enforce fine-grained rules.
- **Policy**: `HashtagCMS\Core\Policies\CmsPolicy` (inherits from `BaseCmsPolicy`).
- **Ownership**: Contributors are restricted to their own content via the `isOwner` check.
- **Creation Fix**: Added support for empty resource checks (e.g., when visiting `/create`), ensuring Contributors can actually start new content.

---

## 3. The "Magic Policy" Pattern (Hybrid Model)

To allow for infinite extensibility without modifying core code, the system uses a hybrid policy model:

1. **Predefined Methods**: Common actions (`read`, `edit`, `delete`, `approve`, `publish`) are explicitly defined for IDE support and custom logic.
2. **Magic Fallback (`__call`)**: Any custom permission added to the database (e.g., `export`, `duplicate`, `assign`) is automatically handled by the `BaseCmsPolicy::__call()` method.
3. **Automatic Gate Resolution**: The magic method forwards the check to the registered Laravel Gate, making the system 100% database-extensible.

---

## 4. Performance & Caching Architecture

To handle high-volume administrative tasks, the permission system implements a multi-tier caching strategy, all governed by the `hashtagcmsadmin` configuration:

| Cache Tier | Storage | Description |
| :--- | :--- | :--- |
| **Tier 1: Global Boot Cache** | Redis/File | Stores all roles and permissions at boot time to eliminate DB queries for Gate definitions. |
| **Tier 2: Global URL Mapping** | Redis/File | Caches the URL-to-Module mapping in the `CmsModuleInfo` middleware to avoid repeating the Longest Prefix Match query on every load. |
| **Tier 3: User Permission Cache** | Session | Stores `cms_permissions` for the duration of the login. Pre-fetched by the middleware to allow **Early Rejection (403)** for unauthorized users. |
| **Tier 4: Request-Level Cache** | Memory | The `Viewer` trait and `BaseCmsPolicy` cache checks during a single request to optimize complex dashboard lists. |

**Configuration Keys**:
- `hashtagcmsadmin.permissions.cache_ttl` (0 to DISABLE; > 0 to ENABLE)
- `hashtagcmsadmin.permissions.cache_key_prefix` (Session Prefix)

---

## 5. Security Summary for Common Roles

| Role | Access Pattern |
| :--- | :--- |
| **Super Admin / Admin** | Bypasses all policy checks (Read/Write everything). |
| **Editor** | Full CRUD on any content within authorized modules. |
| **Approver** | Status-only access: Read, Mark Approved, and Publish. |
| **Contributor** | Full CRUD, but strictly restricted to content they created. |
| **ReadOnly** | View-only access specifically for assigned modules. |
