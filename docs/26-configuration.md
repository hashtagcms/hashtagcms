# Configuration

Configuration files are located in `config/`.

## `hashtagcms.php`
The main configuration file.
-   `namespace`: The root namespace of the package.
-   `context`: The default site context (e.g., `hashtagcms`).
-   `info`: Site metadata (view folder, theme, base path).
    -   `assets_path`: Base URL and path for frontend assets (`js`, `css`, `img`).
-   `media`: Upload paths and drivers.
-   `domains`: Mapping of domains to contexts.
-   `api_secrets`: Keys for API access authentication.
    > **Critical**: The **key** in this array must match your **Site Context** (e.g., `web` or `hashtagcms`), and the **value** must match the `api_secret` provided in the request.
    > Example: `['web' => 'my-secret-key']`.
-   `redirect_with_message_design`: CSS classes for success/error flash messages.
-   `external_service_timeout`: Timeout (seconds) for service module HTTP calls.
-   `additional_middleware`: Global middleware for all Frontend routes.

## `hashtagcmsadmin.php`
Controls the Backend behavior.
-   `cmsInfo`:
    -   `theme`: The active Admin Theme (e.g., `hashtagcms::be.neo`).
    -   `resource_dir`: Path to admin views.
    -   `module_types`: Allowed types (`Static`, `Query`, `Service`, etc.).
-   `imageSupportedByBrowsers`: List of allowed image extensions.
-   `chartPages`: Pagination limits for charts.
-   `json_query_in_query_module`: Enable/Disable JSON syntax for Query Modules.

## `hashtagcmsapi.php`
API specific settings.
-   `login_session`: Token validity duration (e.g., `+ 1 year`).
-   `login_session_expiry_format`: Date format for expiry.

## `hashtagcmscommon.php`
Shared settings.
-   `version`: Current CMS version.

## Environment Variables (.env)
Crucial variables:
```ini
CONTEXT=hashtagcms
API_SECRET=xxx
HASHTAGCMS_LICENSE_KEY=xxx
```
