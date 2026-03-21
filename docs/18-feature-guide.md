# Extended Feature Guide

The core of HashtagCMS is open-source (MIT). Certain advanced capabilities are available as **Extended Features**, enabled by a feature token provided via the `hashtagcms-extended` integration.

## Feature Tiers
1.  **Core**: Full CMS functionality. Open-source and free to use.
2.  **Extended**: Adds MongoDB Support, Analytics/Logging, SSO, Figma integration, and more.

## Activating Extended Features
To activate extended features:
1.  **Activation**: Obtain an activation key from the [HashtagCMS Website](https://www.hashtagcms.org).
2.  **Configure**: Add the key to your `.env` file.
    ```ini
    HASHTAGCMS_FEATURE_TOKEN="ey..."
    ```
3.  **Clear Cache**: Run `php artisan config:clear`.


## Logic
The system uses `HashtagCMSExtended\Models\License` to verify features at runtime.
```php
if (License::hasFeature('mongodb')) {
    // enable mongo driver
}
```

## Validation
Validation checks:
-   **Signature**: Is the token authentically signed?
-   **Expiry**: Is the date valid?
-   **Domain**: Does the request domain match?
