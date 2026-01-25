# Helper Functions Reference

HashtagCMS provides numerous helper functions globally available throughout your application.

## Frontend Helpers

Located in: `src/Core/Helpers/FrontendHelper.php`

### Resource & Paths

#### `htcms_get_resource(string $resource = ''): string`
Get full URL for a resource (uses `MEDIA_URL` from env).

#### `htcms_get_domain_path(string $path = ''): string`
Get full URL including domain for a given path.

#### `htcms_get_path(string $path): string`
Get language/platform aware path.

#### `htcms_get_js_resource(string $path): string`
Get path for JS file in current theme.

#### `htcms_get_css_resource(string $path): string`
Get path for CSS file in current theme.

#### `htcms_get_image_resource(string $path): string`
Get path for Image file in current theme.

---

### Layout & Content

#### `htcms_get_header_menu(string $active = ''): array`
Get header menu structure as array.

#### `htcms_get_header_menu_html(array $data, ?int $maxLimit = null, ?array $css = null): string`
Get header menu as rendered HTML.

#### `htcms_get_body_content(): string`
Get the main body content for the current page.

#### `htcms_get_header_content(bool $reverse = false): string`
Get the header content HTML.

#### `htcms_get_footer_content(bool $reverse = false): string`
Get the footer content HTML.

#### `htcms_get_header_title(): string`
Get the page title.

#### `htcms_get_all_meta_tags(): string`
Get all SEO meta tags.

#### `htcms_parse_string_for_view(string $string = ''): string`
Parse a string and replace dynamic variables.

---

### Context Information

#### `htcms_get_site_info(?string $key = null): mixed`
Get current Site information.

#### `htcms_get_site_id(): int`
Get current Site ID.

#### `htcms_get_lang_info(?string $key = null): mixed`
Get current Language information.

#### `htcms_get_language_id(): int`
Get current Language ID.

#### `htcms_get_platform_info(?string $key = null): mixed`
Get current Platform information.

#### `htcms_get_category_info(?string $key = null): mixed`
Get current Category information.

#### `htcms_get_page_info(?string $key = null): mixed`
Get current Page information.

#### `htcms_get_theme_info(?string $key = null): mixed`
Get current Theme information.

#### `htcms_get_site_props(bool $asJson = false): string|array`
Get all site properties (IDs, names, context) for frontend use (e.g. React/Vue).

#### `htcms_get_shared_data(string $module_alias = ''): mixed`
Get data from a shared module.

---

### Utilities

#### `htcms_trans(string $key): string`
Translate a key (Alias for `____($key)`).

#### `sanitize(string $str = ''): ?string`
Sanitize string (removes script tags).

#### `getFormattedDate(?string $date = null): string`
Get human readable date (e.g. "2 hours ago").

---

## Admin Helpers

Located in: `src/Core/Helpers/AdminHelper.php`

### Configuration & Views

#### `htcms_admin_config(string $key = null, mixed $notCmsInfoObj = null): mixed`
Get admin configuration value.

#### `htcms_admin_view($name, $data = [], $isAjax = false)`
Load an admin view safely.

#### `htcms_admin_get_view_path($name): string`
Get the full view path including theme.

#### `htcms_admin_theme(): string`
Get current admin theme name.

#### `htcms_admin_base_resource(): string`
Get admin resource directory.

#### `htcms_is_view_exist($name): bool`
Check if a view exists.

---

### URLs & Assets

#### `htcms_admin_path(string $name = '', array $queryParams = []): string`
Generate an admin URL path.

#### `htcms_admin_asset(string $url = ''): string`
Get admin asset URL.

#### `htcms_get_media(string $file = ''): string`
Get media file path.

#### `htcms_get_save_path($module_name): string`
Get the 'store' action path for a module.

---

### Session & State

#### `htcms_get_siteId_for_admin()`
Get current active site ID in admin.

#### `htcms_set_siteId_for_admin($id)`
Set current active site ID in admin.

#### `htcms_get_language_id_for_admin()`
Get current language ID in admin.

#### `htcms_set_language_id_for_admin($id)`
Set current language ID in admin.

---

### Utilities

#### `htcms_get_current_date(): string`
Get current date (Y-m-d H:i:s).

#### `htcms_get_module_name($module_info): string`
Get singular module name.
