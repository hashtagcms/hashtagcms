# SSO Integration (Pro+) @todo: Coming soon

Enable Enterprise Single Sign-On (SSO) for your Admin Panel.

## supported Providers
-   Google Workspace (OAuth)
-   Microsoft Azure AD (SAML 2.0)
-   Okta

## Configuration
In `config/hashtagcms.php` (or published config):

```php
'sso' => [
    'enabled' => true,
    'provider' => 'google',
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => '/admin/login/callback',
],
```

## How It Works
1.  Admin Login page shows "Login with SSO" button.
2.  User is redirected to IdP.
3.  On return, HashtagCMS checks if the email exists in `users` table.
4.  If yes, logs them in.
5.  If configured, auto-provisions the user with a default "Editor" role.

