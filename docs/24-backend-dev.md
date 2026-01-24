# Backend Development

HashtagCMS Admin Panel is built on Laravel and VueJs or Blade (standard).

## Extensibility
You can add new menus and features to the Admin Panel without hacking the core.

### 1. Using CMS Module Builder (Recommended)
Instead of manually creating files, use the **Admin > CMS Modules** feature. It uses `CmsmoduleController` behind the scenes to:
1.  Generate the Controller (`cms:controller`).
2.  Generate the Model (`cms:model`).
3.  Register the module in the database.

**Steps:**
1.  Navigate to **Settings > CMS Modules**.
2.  Click **Add New**.
3.  Enter **Controller Name** (e.g., `MyReport`).
4.  Enter **Data Source** (Model).
5.  Ensure **Create Files** is checked.
6.  Save.

> **⚠️ Permission Issue? Not a good practice. Write locally and upload it later**
> If you encounter errors while creating modules, it's likely because the web server doesn't have **Write Access** to generate files.
> Ensure `app/Http/Controllers/Admin` and `app/Models` are writable:
> ```bash
> chmod -R 775 app/Http/Controllers/Admin app/Models
> chown -R www-data:www-data app/Http/Controllers/Admin app/Models
> ```

### 2. Manual Creation (Advanced)
If you need granular control:
```php
php artisan cms:module-controller MyReportController
```

### 2. Create View
Views live in `resources/views/vendor/hashtagcms/be/neo/`.
But for custom modules, create in `resources/views/admin/my-report/index.blade.php`.

## Admin Helpers

These global helpers are available in `src/Core/Helpers/AdminHelper.php`:

### URL & Paths
-   `htcms_admin_path($name, $queryParams)`: Generates admin routes.
-   `htcms_admin_asset($url)`: Gets asset URL.
-   `htcms_get_media($file)`: Gets media URL.
-   `htcms_get_save_path($module_name)`: Returns the 'store' action path for a module.

### Views & Config
-   `htcms_admin_view($name, $data, $isAjax)`: Loads an admin view safely.
-   `htcms_admin_config('key')`: Fetches admin configuration.
-   `htcms_admin_theme()`: Returns current admin theme name.
-   `htcms_is_view_exist($name)`: Checks if a view exists.
-   `htcms_admin_get_view_path($name)`: Resolves full view path (theme prefixed).

### Session / State
-   `htcms_get_siteId_for_admin()`: Get current active site ID.
-   `htcms_set_siteId_for_admin($id)`: Set active site ID.
-   `htcms_get_language_id_for_admin()`: Get current language ID.
-   `htcms_set_language_id_for_admin($id)`: Set current language ID.

### Utilities
-   `htcms_get_current_date()`: Returns `Y-m-d H:i:s`.
-   `htcms_get_module_name($module_info)`: Returns singular name of a module.

## Authentication
The Admin Panel uses the standard `web` guard over `users` table, but typically restricts access via Middleware `auth` + `admin`, ensuring `user->user_type == 'staff'`.
