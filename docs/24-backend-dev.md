# Backend Development

HashtagCMS Admin Panel is built on Laravel and VueJs or Blade (standard).

## Extensibility
You can add new menus and features to the Admin Panel without hacking the core.

### 1. Using the CMS Module Wizard

Run the interactive wizard from the terminal or create a new module from the admin panel:

```bash
php artisan cms:module
```

Or use the **Admin Panel → Settings → CMS Modules → Add New** UI form.

Both paths collect the same information and generate:

| Artefact | Location |
|---|---|
| Controller | `app/Http/Controllers/Admin/{Name}Controller.php` |
| Model | `app/Models/{Name}.php` |
| Related models | `app/Models/{RelatedName}.php` (recursive) |
| FormRequest validator | `app/Http/Requests/{Name}ControllerRequest.php` |
| addedit view | `resources/views/be/modern/{name}/addedit.blade.php` |
| DB module record | `cms_modules` table |

See [22-console-commands.md](./22-console-commands.md) for full wizard details.

> **⚠️ Write permission required for file generation**
> Ensure the web server can write to these directories:
> ```bash
> chmod -R 775 app/Http/Controllers/Admin app/Models app/Http/Requests
> chown -R www-data:www-data app/Http/Controllers/Admin app/Models
> ```

---

### 2. Generated Controller Anatomy

Every generated controller extends `BaseAdminController` and is fully pre-configured:

```php
class CountryController extends BaseAdminController
{
    // Auto-derived from DB table — dot-notation for relations
    protected $dataFields = ['id', 'lang.name', 'zone.name', 'iso_code', 'updated_at'];

    protected $dataSource = Country::class;

    // Auto-derived: 'lang' if lang table exists + one entry per _id column
    protected $dataWith = ['lang', 'zone'];

    protected $actionFields = ['edit', 'delete'];

    // Auto-generated for each _id FK column (populates dropdowns in the edit form)
    protected $bindDataWithAddEdit = [
        'zones'      => ['dataSource' => Zone::class, 'method' => 'all'],
        'currencies' => ['dataSource' => Currency::class, 'method' => 'all'],
    ];

    public function store(Request $request)
    {
        // Policy check, validation rules (from DB schema), $saveData assignments,
        // optional $langData block, and correct save method:
        //   saveData()         — no lang table
        //   saveDataWithLang() — lang table exists
    }
}
```

**Related model namespace resolution** (for `use` imports):
1. Check `App\Models\Zone` — if class exists, use it
2. Fall back to `HashtagCMS\Models\Zone` (package-bundled models like Currency, Zone)
3. If neither found, emit `App\Models\Zone; // TODO` comment

---

### 3. Manual Creation (Advanced)

```bash
php artisan cms:controller MyReport MyReport null '*'
php artisan cms:model MyReport
php artisan cms:validator MyReport MyReportControllerRequest
```

### 4. Create View

Views live in `resources/views/vendor/hashtagcms/be/modern/`.
For custom modules, create: `resources/views/be/modern/my-report/addedit.blade.php`.

---

## Bulk Database Operations (`HasRawDatabaseOps` trait)

All controllers inheriting from `BaseAdminController` have access to two optimized bulk-update methods from the `HasRawDatabaseOps` trait.

### `bulkUpdateIndex()` — single PK tables

Collapses N position updates into **one SQL statement** using `CASE WHEN`:

```php
// Before: N queries (one per row)
// After: 1 query regardless of N
$this->bulkUpdateIndex(
    table:          'cms_modules',
    rows:           [['id' => 5, 'position' => 1], ['id' => 3, 'position' => 2]],
    idColumn:       'id',        // default
    positionColumn: 'position'   // default
);
```

**Generated SQL**:
```sql
UPDATE `cms_modules`
SET `position` = CASE `id`
    WHEN 5 THEN 1
    WHEN 3 THEN 2
END
WHERE `id` IN (5, 3)
```

Used by: `CmsmoduleController`, `FestivalController`, `GalleryController`.

---

### `bulkRawUpdate()` — composite WHERE + flexible SET columns

For tables with composite primary/unique keys (e.g. pivot tables):

```php
$this->bulkRawUpdate('category_site', [
    ['where' => ['category_id' => 5, 'site_id' => 1], 'data' => ['position' => 1]],
    ['where' => ['category_id' => 3, 'site_id' => 1], 'data' => ['position' => 2]],
]);
```

**Generated SQL**:
```sql
UPDATE `category_site`
SET `position` = CASE
    WHEN `category_id` = ? AND `site_id` = ? THEN ?
    WHEN `category_id` = ? AND `site_id` = ? THEN ?
END
WHERE (`category_id` = ? AND `site_id` = ?)
   OR (`category_id` = ? AND `site_id` = ?)
```

- All items must have the same `where` keys and `data` keys.
- Supports updating multiple SET columns simultaneously.
- Wrapped in a transaction with rollback on error.
- Logged via `QueryLogger`.

Used by: `CategoryController`.

---

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

---

## Admin List View Customization

When creating controllers that extend `BaseAdminController`, you can customize the list view actions.

### Standard Actions
```php
protected $actionFields = ['edit', 'delete'];
```

### Custom/More Actions
```php
protected $moreActionFields = [
    [
        'label'               => 'Show all info',
        'action'              => 'showinfo',
        'css'                 => 'js_ajax',
        'icon_css'            => 'fa fa-info-circle',
        'action_append_field' => 'id',
        'hrefAttributes'      => [
            'data-info'          => 'site',
            'data-editable'      => false,
            'data-excludefields' => ['lang', 'category', 'theme'],
        ],
    ]
];
```

**Key `hrefAttributes` for `showinfo` Action:**
- `data-info`: Model backend to fetch data for the Info Popup.
- `data-editable`: If `true`, the `id` field inside the popup links to the edit page.
- `data-excludefields`: Column names to hide from the popup window.

---

## Authentication
The Admin Panel uses the standard `web` guard over `users` table, but restricts access via Middleware `auth` + `admin`, ensuring `user->user_type == 'staff'`.
