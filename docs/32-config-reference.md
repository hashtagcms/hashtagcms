# Configuration Reference

This document provides a comprehensive reference for all configuration keys found in `config/hashtagcms.php` and `config/hashtagcmsadmin.php`.

## `config/hashtagcms.php`

### Root Settings
| Key | Type | Default | Description |
| :--- | :--- | :--- | :--- |
| `namespace` | string | `HashtagCms\` | The root namespace for the CMS package. |
| `context` | string | `hashtagcms` | The default Site Context (e.g., `web`, `app`). Used when domain mapping fails. |
| `blog_per_page` | int | `10` | Number of posts per page in blog modules. |
| `external_service_timeout` | int | `5` | Timeout (seconds) for external Service Module calls. |

---

### `info` Array
General site information.

| Key | Type | Default | Description |
| :--- | :--- | :--- | :--- |
| `site_name` | string | `Hashtag CMS` | Visual name of the site. |
| `view_folder` | string | `hashtagcms::fe` | Root namespace/folder for frontend views. |
| `theme` | string | `fe.default` | Active frontend theme directory. |
| `base_path` | string | `''` | Base path suffix (rarely used). |
| `records_per_page` | int | `20` | Default pagination limit for lists. |
| `media_path` | string | `/storage/media` | Public path for media files. |
| `assets_path` | array | *See below* | Asset configurations. |

**`info.assets_path` Structure:**
```php
'assets_path' => [
    'base_url' => env('ASSET_URL', ''), // CDN URL
    'base_path' => '/assets/hashtagcms/fe', // Local path suffix
    'js' => 'js', // JS folder name
    'css' => 'css', // CSS folder name
    'image' => 'img' // Image folder name
]
```

---

### `media` Array
File upload handling.

| Key | Type | Description |
| :--- | :--- | :--- |
| `upload_path` | string | Physical path (relative to `storage/app`). Default: `public/media`. |
| `http_path` | string | Public URL path. Default: `/storage/media`. |

---

### `domains` Array
Maps Domain Names (Host) to Site Contexts.
```php
'domains' => [
    'localhost' => 'hashtagcms',
    'example.com' => 'web',
    'api.example.com' => 'api_context'
]
```

---

### `api_secrets` Array
Authentication keys for Site Contexts.
**Critical**: Key must match Context.
```php
'api_secrets' => [
    'hashtagcms' => env('API_SECRET', '...'),
    'web' => 'some-secret-key'
]
```

---

### `redirect_with_message_design` Array
CSS classes for controller redirects (`withMessage`).
-   `css_success`: e.g. `alert alert-primary mb-0 appear`
-   `css_error`: e.g. `alert-danger alert mb-0 appear`
-   `css_error_close_button`: Icon class (e.g. `fa fa-times`)

---

## `config/hashtagcmsadmin.php`

### `cmsInfo` Array
Backend configuration.

| Key | Value | Description |
| :--- | :--- | :--- |
| `theme` | `hashtagcms::be.neo` | Admin panel theme. |
| `resource_dir` | `be/neo` | Resource directory relative to assets. |
| `base_context` | `admin` | Helper context. |
| `base_path` | `/admin` | URL prefix for admin routes. |
| `module_types` | `['Static', 'Query', ...]` | Allowed module types in Module Builder. |
| `action_as_ajax` | `['delete', ...]` | Actions that trigger AJAX instead of Page Load. |

### Other Keys
| Key | Type | Description |
| :--- | :--- | :--- |
| `chartPages` | int | Pagination limit for chart data. |
| `imageSupportedByBrowsers` | array | List of valid image extensions (e.g. `jpg`, `webp`). |
| `json_query_in_query_module` | bool | If `true`, allows JSON syntax in Query Module definitions. |
