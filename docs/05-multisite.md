# Multi-Site & Multi-Tenancy

HashtagCMS is designed from the ground up to be a true multi-tenant system. You can host 10 different websites (based on subcriptions), for different clients, on different domains, all from a single Laravel installation and a single database.

## How it Works

### Site Context
The system identifies the "current site" by checking the incoming **Domain Name**.
- If a user visits `example.com`, the system looks up the `sites` table for `domain='example.com'`.
- It sets the global `site_id` to that record's ID.
- All subsequent database queries are automatically filtered by `where site_id = ?`.

### Shared vs. Isolated Data
- **Users (Authors)**: Can be shared or specific. A user can be an Admin on Site A but have no access to Site B.
- **Modules**: You can define a module "Global" to use across all sites, or "Site Specific".
- **Themes**: Each site can have a completely different theme folder.

## Configuration

To add a new site:
1.  **Admin Panel**: Go to **Sites** -> **Add New**.
2.  **Domain**: Enter the domain (e.g., `myshop.com`).
3.  **Context**: Give it a unique short code (e.g., `myshop`).

### Domain Mapping (Localhost)
If you are developing locally, you can map domains in `config/hashtagcms.php`:

```php
'domains' => [
    'localhost' => 'htcms', // Main site context
    'shop.test' => 'myshop', // Second site context
],
```

## Cross-Origin Resource Sharing (CORS)
Since you are serving multiple domains, you must ensure your API allows requests from them. Update `config/cors.php`:
```php
'allowed_origins' => ['http://example.com', 'http://myshop.com'],
```

## Best Practices
- **Media Separation**: By default, media is stored in `public/media`. For stricter separation, you might want to customize the `upload_path` per site context in the config, though the default setup shares the folder for simplicity.
- **Unique API Secrets**: Each site should have its own `API_SECRET` in `.env` or config to prevent unauthorized cross-site data loading.
