# Zero-Database (Stateless) Authentication

HashtagCMS supports a "Zero Dependencies" mode for Frontend (Headless) applications. This allows you to run a frontend instance (e.g., in a Docker container) without needing a local database (like SQLite or MySQL) to handle user sessions.

Instead of writing users to a local database, the system validates the session against the External API and stores the user identity in memory for the request duration.

## How It Works

1.  **Login**: Credentials are sent to the External API. On success, an API Token is returned.
2.  **Session**: The API Token and User Data are stored in the Laravel Session.
3.  **Stateless Provider**: The custom `ExternalApiUserProvider` reconstructs the User object from the Session data on every request, bypassing the database entirely.

## Configuration

To enable this mode, you must update two files in your application.

### 1. Update `.env`

Ensure you have enabled the external API and defined the authentication endpoints.

```env
HASHTAG_CMS_ENABLE_EXTERNAL_API=true
HASHTAG_CMS_CONFIG_API=http://api.yoursite.com/api/hashtagcms/public/configs/v1/site-configs
HASHTAG_CMS_DATA_API=http://api.yoursite.com/api/hashtagcms/public/sites/v1/load-data
HASHTAG_CMS_LOGIN_API=http://api.yoursite.com/api/hashtagcms/public/user/v1/login
HASHTAG_CMS_LOGOUT_API=http://api.yoursite.com/api/hashtagcms/public/user/v1/logout
```

*(Note: The `LoginController` will automatically determine the login/logout URLs from your config, but you can explicitly override them in `config/hashtagcms.php` if needed)*

### 2. Update `config/auth.php`

Change the user provider driver from `eloquent` to `hashtagcms_external_api`.

**File**: `config/auth.php`

```php
'providers' => [
    'users' => [
        // Change the driver from 'eloquent' to our custom provider
        'driver' => 'hashtagcms_external_api', 
        'model' => App\Models\User::class,
    ],
],
```

## Benefits

*   **Docker Safe**: No persistent `database.sqlite` required. Container restarts won't log users out (as long as the session driver is Redis/Cookie/etc).
*   **Security**: No local storage of sensitive user credentials or PII; everything is delegated to the central API.
*   **Performance**: Removes the overhead of a local database query on every request to check `Auth::user()`.
