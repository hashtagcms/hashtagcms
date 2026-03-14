# Feature Tiers

The core of HashtagCMS is open-source (MIT). However, advanced features are gated via a system provided by the `hashtagcms-extended` package.

## Tiers
1.  **Free**: Core CMS functionality.
2.  **Extended**: Adds MongoDB Support & Analytics/Logging.
3.  **Advanced**: All features + Figma Integration (later) + Community Support.

## Installation
To activate Extended or Advanced features:
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
