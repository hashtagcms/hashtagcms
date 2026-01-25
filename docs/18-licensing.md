# Licensing System

The core of HashtagCMS is open-source (MIT). However, enterprise features are gated via a License System provided by the `hashtagcms-pro` package.

## Tiers
1.  **Free**: Core CMS functionality.
2.  **Pro**: Adds MongoDB Support & Analytics/Logging.
3.  **Enterprise**: All features + Figma Integration (later) + Priority Support.

## Installation
To activate Pro or Enterprise features:
1.  **Purchase**: Obtain a license key from the [HashtagCMS Portal](https://www.hashtagcms.org/purchase).
2.  **Configure**: Add the key to your `.env` file.
    ```ini
    HASHTAGCMS_LICENSE_KEY="ey..."
    ```
3.  **Clear Cache**: Run `php artisan config:clear`.


## Logic
The system uses `HashtagCms\Core\Utils\License` to verify features at runtime.
```php
if (License::hasFeature('mongodb')) {
    // enable mongo driver
}
```

## Validation
Validation checks:
-   **Signature**: Is the key authentically signed by us?
-   **Expiry**: Is the date valid?
-   **Domain**: Does the request domain match the license?
