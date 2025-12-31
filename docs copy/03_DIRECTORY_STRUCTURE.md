# Directory Structure

HashtagCMS integrates seamlessly into the standard Laravel structure, but adds specific locations for CMS-related code.

## Package Root (`vendor/hashtagcms/core`)

The core logic lives in the package, but you will mostly interact with the **published** files in your `app/` directory.

## Published Directories

### `app/Http/Controllers/`
-   **`Admin/`**: Controllers for Admin Modules you create.
-   **`Frontend/`**: Controllers for Frontend Modules.

### `resources/views/`
-   **`vendor/hashtagcms/admin/`**: Overridable admin views.
-   **`themes/`**: Contains your frontend themes.
    -   `themes/{theme_name}/layouts/`
    -   `themes/{theme_name}/partials/`
    -   `themes/{theme_name}/modules/`: Views for specific modules for this theme.

### `public/`
-   **`assets/hashtagcms/`**: Admin panel assets (JS/CSS).
-   **`assets/{theme_name}/`**: Compiled assets for your frontend themes.

## Concept: "Modules as Folders"

When you create a module (e.g., `HeroBanner`), the CMS expects a specific structure if you follow the "Service" pattern, but generally, it relies on the **Class Namespace**.

By default, Frontend modules are expected in `App\Http\Controllers\Frontend`.
Admin modules are expected in `App\Http\Controllers\Admin`.

You can customize this in `config/hashtagcms.php`.
