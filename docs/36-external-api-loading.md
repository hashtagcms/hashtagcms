# External API Loading Functionality

HashtagCMS supports loading configuration and data from an External API source, enabling Headless and Microservice architectures. This functionality allows a frontend application (or an instance of HashtagCMS) to fetch its entire site structure, content, and settings from a central HashtagCMS server.

## Overview

The External API loading system relies on a secure handshake between the **Client** (the site requesting data) and the **Server** (the central CMS API). This is managed via:

1.  **Site Context**: A unique identifier for each site (e.g., `web`, `app`, `blog`).
2.  **API Secret**: A secure key shared between the Client and the Server, specific to the Site Context.

To enable and configure External API loading, you must update your `config/hashtagcms.php` file on the **Client**. These settings are nested under the `externals` array:

```php
'externals' => [
    'enable_external_api' => env('HASHTAGCMS_ENABLE_EXTERNAL_API', false),
    'external_api_base_url' => env('HASHTAGCMS_EXTERNAL_API_BASE_URL', env('APP_URL')),
    // ... other endpoints
],
```

To enable External API loading, you need to configure the connection on the **Client** application (the one fetching the data).

### 1. Enable External API
Update your `.env` file to point to the remote HashtagCMS API:

```env
HASHTAGCMS_ENABLE_EXTERNAL_API=true
HASHTAGCMS_CONFIG_API=https://api.yoursite.com/api/hashtagcms/public/configs/v1/site-configs
HASHTAGCMS_DATA_API=https://api.yoursite.com/api/hashtagcms/public/sites/v1/load-data
```

### 2. Define Domains
In `config/hashtagcms.php`, the `domains` array maps the **Hostname** to a **Context**.
*   **Key**: The domain/host of your client application.
*   **Value**: The site context name.

```php
'domains' => [
    // format: 'domain.com' => 'context'
    'www.my-client-site.com' => 'htcms',
    'localhost' => 'htcms',
],
```

## How It Works

When `HashtagCMS` initializes (via `BaseInfo` middleware), it checks if `HASHTAG_CMS_ENABLE_EXTERNAL_API` is `true`.

### 1. Context Resolution
The system determines the current **Site Context** based on the request domain (using the `domains` config).
*   Example: Request to `www.mysite.com` -> Context: `web`.

### 2. API Request
The system sends a request to the configured External API URL with the following parameters:
*   `site`: The resolved context (e.g., `web`).
*   **Query Parameters**: All original query parameters from the client request (e.g., `?page=2`, `?category=news`) are merged and forwarded to the API.

### 3. Response Handling
The configuration or data returned by the API is processed and used to render the page, just as if it came from the local database.

## Troubleshooting

### Error: "Site Not Found"
*   **Cause**: The current site context (determined by the domain) does not exist in the database or domain mapping.
*   **Fix**: Ensure domain is correctly mapped in `config/hashtagcms.php` under `domains`.

### Data Not Updating
*   **Cause**: Caching or missing query parameters.
*   **Fix**: The refactored `DataLoader` ensures query parameters are forwarded. Clear the cache on both Client and Server if issues persist.
